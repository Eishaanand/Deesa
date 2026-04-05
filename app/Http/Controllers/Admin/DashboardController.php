<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAnalyticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(AdminAnalyticsService $analytics): View
    {
        return view('admin.dashboard', [
            'metrics' => $analytics->dashboardMetrics(),
            'live' => $analytics->liveExamMonitoring(),
            'distribution' => $analytics->scoreDistribution(),
            'sales' => $analytics->salesDashboard(),
            'alerts' => $analytics->alerts(),
            'notifications' => $analytics->recentNotifications(),
        ]);
    }
}
