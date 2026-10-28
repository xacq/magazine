<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingestion_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 32)->index();
            $table->string('source_name', 100)->index();
            $table->string('idempotency_key', 191);
            $table->string('payload_hash', 64);
            $table->string('status', 32)->default('processing')->index();
            $table->unsignedSmallInteger('received_count')->default(0);
            $table->unsignedSmallInteger('created_count')->default(0);
            $table->unsignedSmallInteger('updated_count')->default(0);
            $table->unsignedSmallInteger('duplicate_count')->default(0);
            $table->unsignedSmallInteger('pending_update_count')->default(0);
            $table->json('result')->nullable();
            $table->json('errors')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['type', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingestion_runs');
    }
};
