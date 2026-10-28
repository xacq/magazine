<?php

use App\Http\Controllers\Api\V1\ArticleIngestionController;
use App\Http\Controllers\Api\V1\JobIngestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/ingest')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/articles/batch', ArticleIngestionController::class)
        ->middleware('abilities:articles:ingest')
        ->name('api.v1.ingest.articles');

    Route::post('/jobs/batch', JobIngestionController::class)
        ->middleware('abilities:jobs:ingest')
        ->name('api.v1.ingest.jobs');
});
