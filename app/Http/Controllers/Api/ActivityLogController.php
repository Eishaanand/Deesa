<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request, ActivityLogService $service): JsonResponse
    {
        $validated = $request->validate([
            'event' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'meta' => ['nullable', 'array'],
        ]);

        $log = $service->log($request->user(), $validated, $request);

        return response()->json([
            'logged' => true,
            'id' => $log->id,
        ]);
    }
}
