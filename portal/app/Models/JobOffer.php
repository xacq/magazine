<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    protected $fillable = [
        'company', 'title', 'slug', 'sector', 'location', 'work_mode',
        'employment_type', 'salary_min', 'salary_max', 'currency', 'summary',
        'description', 'apply_url', 'status', 'is_featured', 'published_at', 'expires_at',
        'source_name', 'source_external_id', 'source_url', 'content_hash',
        'source_published_at', 'first_seen_at', 'last_seen_at', 'metadata',
        'ingestion_run_id',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'source_published_at' => 'datetime',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSalaryLabelAttribute(): string
    {
        if (! $this->salary_min && ! $this->salary_max) {
            return 'Salario a convenir';
        }

        if ($this->salary_min && $this->salary_max) {
            return sprintf('%s %s – %s', $this->currency, number_format($this->salary_min), number_format($this->salary_max));
        }

        return sprintf('Desde %s %s', $this->currency, number_format($this->salary_min ?? $this->salary_max));
    }
}
