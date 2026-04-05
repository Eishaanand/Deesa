<?php

namespace App\Http\Controllers;

use App\Models\UserExam;
use App\Services\StudentAnalyticsService;
use App\Services\SubscriptionService;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function __invoke(UserExam $attempt, StudentAnalyticsService $analytics): View
    {
        abort_unless($attempt->user_id === auth()->id() || auth()->user()?->isAdmin(), 403);

        $attempt->load('exam.sections.questions', 'answers.question');

        return view('results.show', [
            'attempt' => $attempt,
            'report' => $analytics->attemptReport($attempt),
            'subscriptionPrice' => SubscriptionService::MONTHLY_PRICE_GBP,
        ]);
    }
}
