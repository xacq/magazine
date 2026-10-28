<?php

namespace App\Services;

use App\Models\Article;
use App\Models\IngestionRun;
use App\Models\JobOffer;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class IngestionService
{
    public function articles(array $payload, ?string $idempotencyKey, User $user): array
    {
        return $this->execute('articles', $payload, $idempotencyKey, $user, function (IngestionRun $run) use ($payload): array {
            return $this->ingestArticles($payload['source'], $payload['items'], $run);
        });
    }

    public function jobs(array $payload, ?string $idempotencyKey, User $user): array
    {
        return $this->execute('jobs', $payload, $idempotencyKey, $user, function (IngestionRun $run) use ($payload): array {
            return $this->ingestJobs($payload['source'], $payload['items'], $run);
        });
    }

    private function execute(
        string $type,
        array $payload,
        ?string $idempotencyKey,
        User $user,
        Closure $callback,
    ): array {
        $key = trim((string) $idempotencyKey);

        if ($key === '') {
            $key = (string) Str::uuid();
        }

        if (mb_strlen($key) > 191) {
            throw ValidationException::withMessages([
                'Idempotency-Key' => 'El encabezado Idempotency-Key no puede superar 191 caracteres.',
            ]);
        }

        $payloadHash = $this->hash($payload);
        $existingRun = IngestionRun::query()
            ->where('type', $type)
            ->where('idempotency_key', $key)
            ->first();

        if ($existingRun) {
            if (! hash_equals($existingRun->payload_hash, $payloadHash)) {
                throw new HttpResponseException(response()->json([
                    'message' => 'La Idempotency-Key ya fue utilizada con un contenido diferente.',
                    'run_id' => $existingRun->id,
                ], 409));
            }

            if ($existingRun->status !== 'completed') {
                throw new HttpResponseException(response()->json([
                    'message' => 'La importación con esta Idempotency-Key todavía no está completada.',
                    'run_id' => $existingRun->id,
                    'status' => $existingRun->status,
                ], 409));
            }

            return array_merge($existingRun->result ?? [], ['idempotent_replay' => true]);
        }

        $run = IngestionRun::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'source_name' => $payload['source'],
            'idempotency_key' => $key,
            'payload_hash' => $payloadHash,
            'status' => 'processing',
            'received_count' => count($payload['items']),
            'started_at' => now(),
        ]);

        try {
            $result = DB::transaction(function () use ($callback, $key, $payload, $run, $type): array {
                $batch = $callback($run);
                $result = [
                    'run_id' => $run->id,
                    'idempotency_key' => $key,
                    'type' => $type,
                    'source' => $payload['source'],
                    'status' => 'completed',
                    'counts' => $batch['counts'],
                    'items' => $batch['items'],
                    'idempotent_replay' => false,
                ];

                $run->update([
                    'status' => 'completed',
                    'created_count' => $batch['counts']['created'],
                    'updated_count' => $batch['counts']['updated'],
                    'duplicate_count' => $batch['counts']['duplicates'],
                    'pending_update_count' => $batch['counts']['pending_updates'],
                    'result' => $result,
                    'completed_at' => now(),
                ]);

                return $result;
            });

            return $result;
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'errors' => [['message' => $exception->getMessage()]],
                'completed_at' => now(),
            ]);

            throw $exception;
        }
    }

    private function ingestArticles(string $source, array $items, IngestionRun $run): array
    {
        $result = $this->emptyBatchResult();

        foreach ($items as $item) {
            $attributes = [
                'title' => trim($item['title']),
                'excerpt' => trim($item['excerpt']),
                'body' => $this->safeParagraphs($item['body']),
                'category' => trim($item['category']),
                'image_url' => $item['image_url'] ?? null,
                'source_name' => $source,
                'source_external_id' => $item['external_id'],
                'source_url' => $item['source_url'],
                'source_published_at' => $item['published_at'] ?? null,
                'metadata' => $item['metadata'] ?? [],
            ];
            $contentHash = $this->hash($attributes);
            $article = Article::query()
                ->where('source_name', $source)
                ->where('source_external_id', $item['external_id'])
                ->first();

            if (! $article) {
                $article = Article::query()->create(array_merge($attributes, [
                    'slug' => $this->uniqueSlug(Article::class, $item['title'], $item['external_id']),
                    'content_hash' => $contentHash,
                    'status' => 'pending_review',
                    'is_featured' => false,
                    'published_at' => null,
                    'imported_at' => now(),
                    'ingestion_run_id' => $run->id,
                ]));
                $action = 'created';
            } elseif ($article->content_hash === $contentHash || data_get($article->metadata, 'pending_update_hash') === $contentHash) {
                $action = 'duplicates';
            } elseif ($article->status === 'published') {
                $this->storePendingUpdate($article, $attributes, $contentHash, $run);
                $action = 'pending_updates';
            } else {
                $article->update(array_merge($attributes, [
                    'content_hash' => $contentHash,
                    'status' => 'pending_review',
                    'is_featured' => false,
                    'published_at' => null,
                    'imported_at' => now(),
                    'ingestion_run_id' => $run->id,
                ]));
                $action = 'updated';
            }

            $result['counts'][$action]++;
            $result['items'][] = [
                'external_id' => $item['external_id'],
                'id' => $article->id,
                'slug' => $article->slug,
                'action' => Str::singular($action),
            ];
        }

        return $result;
    }

    private function ingestJobs(string $source, array $items, IngestionRun $run): array
    {
        $result = $this->emptyBatchResult();

        foreach ($items as $item) {
            $attributes = [
                'company' => trim($item['company']),
                'title' => trim($item['title']),
                'sector' => trim($item['sector']),
                'location' => trim($item['location']),
                'work_mode' => $item['work_mode'],
                'employment_type' => trim($item['employment_type']),
                'salary_min' => $item['salary_min'] ?? null,
                'salary_max' => $item['salary_max'] ?? null,
                'currency' => strtoupper($item['currency'] ?? 'USD'),
                'summary' => trim($item['summary']),
                'description' => $this->safeParagraphs($item['description']),
                'apply_url' => $item['apply_url'],
                'source_name' => $source,
                'source_external_id' => $item['external_id'],
                'source_url' => $item['source_url'],
                'source_published_at' => $item['published_at'] ?? null,
                'expires_at' => $item['expires_at'] ?? null,
                'metadata' => $item['metadata'] ?? [],
            ];
            $contentHash = $this->hash($attributes);
            $job = JobOffer::query()
                ->where('source_name', $source)
                ->where('source_external_id', $item['external_id'])
                ->first();

            if (! $job) {
                $job = JobOffer::query()->create(array_merge($attributes, [
                    'slug' => $this->uniqueSlug(JobOffer::class, $item['title'].' '.$item['company'], $item['external_id']),
                    'content_hash' => $contentHash,
                    'status' => 'pending_review',
                    'is_featured' => false,
                    'published_at' => null,
                    'first_seen_at' => now(),
                    'last_seen_at' => now(),
                    'ingestion_run_id' => $run->id,
                ]));
                $action = 'created';
            } elseif ($job->content_hash === $contentHash || data_get($job->metadata, 'pending_update_hash') === $contentHash) {
                $job->update(['last_seen_at' => now()]);
                $action = 'duplicates';
            } elseif ($job->status === 'open') {
                $this->storePendingUpdate($job, $attributes, $contentHash, $run);
                $job->update(['last_seen_at' => now()]);
                $action = 'pending_updates';
            } else {
                $job->update(array_merge($attributes, [
                    'content_hash' => $contentHash,
                    'status' => 'pending_review',
                    'is_featured' => false,
                    'published_at' => null,
                    'last_seen_at' => now(),
                    'ingestion_run_id' => $run->id,
                ]));
                $action = 'updated';
            }

            $result['counts'][$action]++;
            $result['items'][] = [
                'external_id' => $item['external_id'],
                'id' => $job->id,
                'slug' => $job->slug,
                'action' => Str::singular($action),
            ];
        }

        return $result;
    }

    private function storePendingUpdate(Model $model, array $attributes, string $contentHash, IngestionRun $run): void
    {
        $metadata = $model->metadata ?? [];
        $metadata['pending_update'] = $attributes;
        $metadata['pending_update_hash'] = $contentHash;
        $metadata['pending_update_at'] = now()->toIso8601String();

        $model->update([
            'metadata' => $metadata,
            'ingestion_run_id' => $run->id,
        ]);
    }

    private function safeParagraphs(string $content): string
    {
        $withBreaks = preg_replace('/<\/?(?:p|div|h[1-6]|li|ul|ol|blockquote|br)\b[^>]*>/i', "\n\n", $content);
        $plain = html_entity_decode(strip_tags($withBreaks ?? $content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $paragraphs = preg_split('/(?:\r\n|\r|\n){2,}/', trim($plain)) ?: [];

        return collect($paragraphs)
            ->map(fn (string $paragraph): string => trim($paragraph))
            ->filter()
            ->map(fn (string $paragraph): string => '<p>'.nl2br(e($paragraph)).'</p>')
            ->implode('');
    }

    private function uniqueSlug(string $modelClass, string $title, string $externalId): string
    {
        $base = Str::slug($title) ?: 'contenido';

        if (! $modelClass::query()->where('slug', $base)->exists()) {
            return $base;
        }

        return Str::limit($base, 220, '').'-'.substr(hash('sha256', $externalId), 0, 8);
    }

    private function hash(array $data): string
    {
        return hash('sha256', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    private function emptyBatchResult(): array
    {
        return [
            'counts' => [
                'created' => 0,
                'updated' => 0,
                'duplicates' => 0,
                'pending_updates' => 0,
            ],
            'items' => [],
        ];
    }
}
