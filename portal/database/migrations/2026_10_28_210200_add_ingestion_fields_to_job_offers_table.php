<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->foreignId('ingestion_run_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('source_name', 100)->nullable()->after('apply_url');
            $table->string('source_external_id', 191)->nullable()->after('source_name');
            $table->string('source_url')->nullable()->after('source_external_id');
            $table->string('content_hash', 64)->nullable()->after('source_url')->index();
            $table->timestamp('source_published_at')->nullable()->after('content_hash');
            $table->timestamp('first_seen_at')->nullable()->after('source_published_at');
            $table->timestamp('last_seen_at')->nullable()->after('first_seen_at');
            $table->json('metadata')->nullable()->after('last_seen_at');

            $table->unique(['source_name', 'source_external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropUnique(['source_name', 'source_external_id']);
            $table->dropForeign(['ingestion_run_id']);
            $table->dropColumn([
                'ingestion_run_id', 'source_name', 'source_external_id', 'source_url',
                'content_hash', 'source_published_at', 'first_seen_at', 'last_seen_at', 'metadata',
            ]);
        });
    }
};
