<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IngestionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_ingestion_requires_authentication(): void
    {
        $this->postJson('/api/v1/ingest/articles/batch', $this->articlePayload())
            ->assertUnauthorized();
    }

    public function test_token_requires_the_correct_ability(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['jobs:ingest']);

        $this->postJson('/api/v1/ingest/articles/batch', $this->articlePayload())
            ->assertForbidden();
    }

    public function test_article_batch_is_created_as_pending_review_and_sanitized(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['articles:ingest']);
        $payload = $this->articlePayload();
        $payload['items'][0]['body'] = '<p>Introducción</p><script>alert("xss")</script><p>Cierre</p>';

        $this->withHeader('Idempotency-Key', 'articles-run-1')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk()
            ->assertJsonPath('counts.created', 1)
            ->assertJsonPath('counts.updated', 0)
            ->assertJsonPath('idempotent_replay', false);

        $article = Article::query()->firstOrFail();

        $this->assertSame('pending_review', $article->status);
        $this->assertNull($article->published_at);
        $this->assertStringNotContainsString('<script', $article->body);
        $this->assertStringContainsString('<p>', $article->body);
        $this->assertDatabaseCount('ingestion_runs', 1);
    }

    public function test_same_idempotency_key_replays_without_writing_again(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['articles:ingest']);
        $payload = $this->articlePayload();

        $this->withHeader('Idempotency-Key', 'stable-key')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk()
            ->assertJsonPath('idempotent_replay', false);

        $this->withHeader('Idempotency-Key', 'stable-key')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk()
            ->assertJsonPath('idempotent_replay', true);

        $this->assertDatabaseCount('articles', 1);
        $this->assertDatabaseCount('ingestion_runs', 1);
    }

    public function test_reusing_idempotency_key_with_different_payload_is_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['articles:ingest']);
        $payload = $this->articlePayload();

        $this->withHeader('Idempotency-Key', 'conflicting-key')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk();

        $payload['items'][0]['title'] = 'Otro título';

        $this->withHeader('Idempotency-Key', 'conflicting-key')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertStatus(409);
    }

    public function test_changed_draft_is_updated_without_creating_a_duplicate(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['articles:ingest']);
        $payload = $this->articlePayload();

        $this->withHeader('Idempotency-Key', 'draft-v1')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk();

        $payload['items'][0]['title'] = 'Título corregido';

        $this->withHeader('Idempotency-Key', 'draft-v2')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk()
            ->assertJsonPath('counts.updated', 1);

        $this->assertDatabaseCount('articles', 1);
        $this->assertSame('Título corregido', Article::query()->firstOrFail()->title);
    }

    public function test_published_article_is_not_overwritten_by_a_source_update(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['articles:ingest']);
        $payload = $this->articlePayload();

        $this->withHeader('Idempotency-Key', 'published-v1')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk();

        $article = Article::query()->firstOrFail();
        $article->update(['status' => 'published', 'published_at' => now()]);
        $originalTitle = $article->title;
        $payload['items'][0]['title'] = 'Título recibido posteriormente';

        $this->withHeader('Idempotency-Key', 'published-v2')
            ->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertOk()
            ->assertJsonPath('counts.pending_updates', 1);

        $article->refresh();
        $this->assertSame($originalTitle, $article->title);
        $this->assertSame('Título recibido posteriormente', data_get($article->metadata, 'pending_update.title'));
    }

    public function test_job_batch_is_created_and_duplicate_is_detected(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['jobs:ingest']);
        $payload = $this->jobPayload();

        $this->withHeader('Idempotency-Key', 'jobs-run-1')
            ->postJson('/api/v1/ingest/jobs/batch', $payload)
            ->assertOk()
            ->assertJsonPath('counts.created', 1);

        $this->withHeader('Idempotency-Key', 'jobs-run-2')
            ->postJson('/api/v1/ingest/jobs/batch', $payload)
            ->assertOk()
            ->assertJsonPath('counts.duplicates', 1);

        $job = JobOffer::query()->firstOrFail();
        $this->assertSame('pending_review', $job->status);
        $this->assertNotNull($job->last_seen_at);
        $this->assertDatabaseCount('job_offers', 1);
    }

    public function test_invalid_batch_is_rejected_before_creating_a_run(): void
    {
        Sanctum::actingAs(User::factory()->create(), ['articles:ingest']);
        $payload = $this->articlePayload();
        unset($payload['items'][0]['source_url']);

        $this->postJson('/api/v1/ingest/articles/batch', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.source_url');

        $this->assertDatabaseCount('articles', 0);
        $this->assertDatabaseCount('ingestion_runs', 0);
    }

    private function articlePayload(): array
    {
        return [
            'source' => 'Fuente de prueba',
            'items' => [[
                'external_id' => 'article-001',
                'title' => 'Ecuador fortalece su ecosistema digital',
                'excerpt' => 'Un resumen original preparado para el portal.',
                'body' => "Primer párrafo.\n\nSegundo párrafo.",
                'category' => 'Tecnología',
                'image_url' => 'https://example.com/image.jpg',
                'source_url' => 'https://example.com/noticias/article-001',
                'published_at' => '2026-10-28T10:00:00-05:00',
                'metadata' => ['language' => 'es'],
            ]],
        ];
    }

    private function jobPayload(): array
    {
        return [
            'source' => 'Bolsa autorizada',
            'items' => [[
                'external_id' => 'job-001',
                'company' => 'Empresa Demo',
                'title' => 'Desarrollador Laravel',
                'sector' => 'Tecnología',
                'location' => 'Guayaquil, Ecuador',
                'work_mode' => 'Híbrido',
                'employment_type' => 'Tiempo completo',
                'salary_min' => 1200,
                'salary_max' => 1800,
                'currency' => 'USD',
                'summary' => 'Vacante para desarrollar productos web.',
                'description' => "Experiencia con PHP y Laravel.\n\nTrabajo en equipo.",
                'apply_url' => 'https://example.com/jobs/job-001/apply',
                'source_url' => 'https://example.com/jobs/job-001',
                'published_at' => '2026-10-28T09:00:00-05:00',
                'expires_at' => '2026-11-28T23:59:59-05:00',
                'metadata' => ['country' => 'EC'],
            ]],
        ];
    }
}
