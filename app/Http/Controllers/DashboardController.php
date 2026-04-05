<?php

namespace App\Http\Controllers;

use App\Services\StudentAnalyticsService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, StudentAnalyticsService $analytics): View
    {
        return view('dashboard', [
            'overview' => $analytics->dashboardOverview($request->user()),
            'subscriptionPrice' => SubscriptionService::MONTHLY_PRICE_GBP,
        ]);
    }
}
