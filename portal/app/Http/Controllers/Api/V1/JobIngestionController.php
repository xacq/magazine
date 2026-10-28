<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IngestJobsRequest;
use App\Models\User;
use App\Services\IngestionService;
use Illuminate\Http\JsonResponse;

class JobIngestionController extends Controller
{
    public function __invoke(IngestJobsRequest $request, IngestionService $service): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json(
            $service->jobs($request->validated(), $request->header('Idempotency-Key'), $user)
        );
    }
}
