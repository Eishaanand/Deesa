<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;
use Illuminate\Http\JsonResponse;

class LiveMonitoringController extends Controller
{
    public function __invoke(AdminAnalyticsService $analytics): JsonResponse
    {
        return response()->json($analytics->liveExamMonitoring());
    }
}
