<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Collection;

class ExamAccessService
{
    public function mapForUser(User $user, Collection $exams): Collection
    {
        $submittedCount = $user->examAttempts()->where('status', 'submitted')->distinct('exam_id')->count('exam_id');

        return $exams
            ->sortBy('sequence_number')
            ->values()
            ->map(function (Exam $exam) use ($submittedCount, $user) {
                $unlocked = $exam->sequence_number <= ($submittedCount + 1);
                $subscriptionRequired = $exam->requires_subscription && ! $user->hasActivePremium();

                return [
                    'exam' => $exam,
                    'unlocked' => $unlocked,
                    'subscription_required' => $subscriptionRequired,
                    'can_start' => $unlocked && ! $subscriptionRequired,
                    'show_premium_prompt' => $exam->sequence_number > 3 && $submittedCount >= 3 && ! $user->hasActivePremium(),
                ];
            });
    }

    public function canStart(User $user, Exam $exam): bool
    {
        $submittedCount = $user->examAttempts()->where('status', 'submitted')->distinct('exam_id')->count('exam_id');

        if ($exam->sequence_number > ($submittedCount + 1)) {
            return false;
        }

        if ($exam->requires_subscription && ! $user->hasActivePremium()) {
            return false;
        }

        return true;
    }
}
