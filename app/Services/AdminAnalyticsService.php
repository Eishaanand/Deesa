<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserExam;
use App\Services\SubscriptionService;

class AdminAnalyticsService
{
    public function dashboardMetrics(): array
    {
        $students = User::where('role', UserRole::Student)->count();
        $activeUsers = User::where('last_seen_at', '>=', now()->subMinutes(15))->count();
        $attempts = UserExam::query();
        $premiumUsers = User::where('subscription_status', 'premium')->count();

        return [
            'total_users' => User::count(),
            'total_students' => $students,
            'premium_users' => $premiumUsers,
            'free_users' => max(User::count() - $premiumUsers, 0),
            'active_users' => $activeUsers,
            'average_score' => round((float) $attempts->avg('total_score'), 2),
            'average_accuracy' => round((float) $attempts->avg('accuracy_percentage'), 2),
            'monthly_recurring_revenue' => $premiumUsers * SubscriptionService::MONTHLY_PRICE_GBP,
            'projected_annual_revenue' => $premiumUsers * SubscriptionService::MONTHLY_PRICE_GBP * 12,
        ];
    }

    public function scoreDistribution(): array
    {
        $buckets = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];

        UserExam::whereNotNull('accuracy_percentage')->get()->each(function (UserExam $attempt) use (&$buckets): void {
            $accuracy = $attempt->accuracy_percentage;
            match (true) {
                $accuracy <= 20 => $buckets['0-20']++,
                $accuracy <= 40 => $buckets['21-40']++,
                $accuracy <= 60 => $buckets['41-60']++,
                $accuracy <= 80 => $buckets['61-80']++,
                default => $buckets['81-100']++,
            };
        });

        return $buckets;
    }

    public function liveExamMonitoring(): array
    {
        return UserExam::with(['user', 'exam', 'currentSection'])
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->get()
            ->map(fn (UserExam $attempt) => [
                'student' => $attempt->user->name,
                'exam' => $attempt->exam->title,
                'section' => $attempt->currentSection?->name,
                'started_at' => optional($attempt->started_at)->toDateTimeString(),
                'time_elapsed_minutes' => optional($attempt->started_at)?->diffInMinutes(now()),
            ])->all();
    }

    public function studentDeepDive(User $user): array
    {
        $attempts = $user->examAttempts()->with('exam')->latest()->get();

        return [
            'logins' => $user->activityLogs()->where('event', 'login')->latest()->take(10)->get(),
            'sessions' => $user->activityLogs()->latest()->take(20)->get(),
            'attempt_count' => $attempts->count(),
            'accuracy_trend' => $attempts->take(10)->reverse()->map(fn (UserExam $attempt) => [
                'exam' => $attempt->exam->title,
                'accuracy' => $attempt->accuracy_percentage,
            ])->values()->all(),
            'weak_sections' => collect($attempts)
                ->flatMap(fn (UserExam $attempt) => $attempt->analytics['section_breakdown'] ?? [])
                ->groupBy('section')
                ->map(fn ($items, $section) => [
                    'section' => $section,
                    'accuracy' => round(collect($items)->avg('accuracy_percentage'), 2),
                ])
                ->sortBy('accuracy')
                ->take(3)
                ->values()
                ->all(),
        ];
    }

    public function recentNotifications(): array
    {
        return Notification::with(['user', 'sender'])
            ->latest('sent_at')
            ->take(8)
            ->get()
            ->map(fn (Notification $notification) => [
                'title' => $notification->title,
                'audience' => $notification->audience,
                'recipient' => $notification->user?->email ?? 'broadcast',
                'sent_by' => $notification->sender?->name ?? 'system',
                'sent_at' => optional($notification->sent_at)->format('d M Y h:i A'),
            ])->all();
    }

    public function alerts(): array
    {
        $alerts = [];

        $pausedAttempts = UserExam::with(['user', 'exam'])
            ->where('status', 'paused')
            ->where('paused_at', '<=', now()->subMinutes(30))
            ->take(5)
            ->get();

        foreach ($pausedAttempts as $attempt) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'Paused exam needs attention',
                'message' => "{$attempt->user->name} paused {$attempt->exam->title} more than 30 minutes ago.",
            ];
        }

        $expiringPremium = User::where('subscription_status', 'premium')
            ->whereBetween('premium_until', [now(), now()->addDays(3)])
            ->take(5)
            ->get();

        foreach ($expiringPremium as $user) {
            $alerts[] = [
                'level' => 'info',
                'title' => 'Premium renewal risk',
                'message' => "{$user->email} premium access expires on {$user->premium_until?->format('d M Y')}.",
            ];
        }

        if ($this->dashboardMetrics()['premium_users'] === 0) {
            $alerts[] = [
                'level' => 'critical',
                'title' => 'No paying subscribers',
                'message' => 'Monthly recurring revenue is currently GBP 0. Conversion attention required.',
            ];
        }

        return $alerts;
    }

    public function salesDashboard(): array
    {
        $premiumUsers = User::where('subscription_status', 'premium')->get();
        $freeUsers = User::where('subscription_status', 'free')->count();
        $totalUsers = max(User::count(), 1);

        return [
            'mrr' => $premiumUsers->count() * SubscriptionService::MONTHLY_PRICE_GBP,
            'arr' => $premiumUsers->count() * SubscriptionService::MONTHLY_PRICE_GBP * 12,
            'conversion_rate' => round(($premiumUsers->count() / $totalUsers) * 100, 2),
            'free_users' => $freeUsers,
            'premium_users' => $premiumUsers->count(),
            'recent_premium_users' => $premiumUsers->sortByDesc('premium_until')->take(6)->values()->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'premium_until' => optional($user->premium_until)->format('d M Y'),
            ])->all(),
        ];
    }
}
