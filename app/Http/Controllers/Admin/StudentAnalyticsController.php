<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAnalyticsService;
use Illuminate\View\View;

class StudentAnalyticsController extends Controller
{
    public function __invoke(User $user, AdminAnalyticsService $analytics): View
    {
        return view('admin.students.show', [
            'student' => $user->load('examAttempts.exam', 'activityLogs'),
            'report' => $analytics->studentDeepDive($user),
        ]);
    }
}
