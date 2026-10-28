<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'category', 'image_url',
        'source_name', 'source_url', 'status', 'is_featured', 'published_at',
        'source_external_id', 'content_hash', 'source_published_at',
        'imported_at', 'metadata', 'ingestion_run_id',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'source_published_at' => 'datetime',
            'imported_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
