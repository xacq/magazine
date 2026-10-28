<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngestionRun extends Model
{
    protected $fillable = [
        'user_id', 'type', 'source_name', 'idempotency_key', 'payload_hash',
        'status', 'received_count', 'created_count', 'updated_count',
        'duplicate_count', 'pending_update_count', 'result', 'errors',
        'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'result' => 'array',
            'errors' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
