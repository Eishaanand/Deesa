<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserExam;

class StudentAnalyticsService
{
    public function dashboardOverview(User $user): array
    {
        $attempts = $user->examAttempts()->with('exam')->latest()->get();
        $submittedAttempts = $attempts->where('status', 'submitted')->values();
        $trend = $submittedAttempts->take(6)->reverse()->map(fn (UserExam $attempt) => [
            'label' => optional($attempt->submitted_at)->format('M d'),
            'score' => (float) $attempt->total_score,
            'accuracy' => (float) $attempt->accuracy_percentage,
        ])->values();
        $latestAttempt = $submittedAttempts->first();
        $latestBreakdown = collect($latestAttempt?->analytics['section_breakdown'] ?? []);

        return [
            'attempts' => $attempts,
            'latest_score' => (float) optional($submittedAttempts->first())->total_score,
            'average_accuracy' => round((float) $submittedAttempts->avg('accuracy_percentage'), 2),
            'trend' => $trend->all(),
            'weak_sections' => $this->weakSections($submittedAttempts),
            'submitted_count' => $submittedAttempts->count(),
            'active_attempt' => $attempts->firstWhere('status', 'in_progress') ?? $attempts->firstWhere('status', 'paused'),
            'latest_marks' => [
                'score' => (float) optional($latestAttempt)->total_score,
                'out_of' => (int) ($latestAttempt->analytics['total_questions'] ?? 0),
                'accuracy' => (float) optional($latestAttempt)->accuracy_percentage,
            ],
            'section_marks' => $latestBreakdown->map(fn (array $section) => [
                'section' => $section['section'],
                'score' => $section['score'],
                'out_of' => $section['total_questions'],
                'accuracy_percentage' => $section['accuracy_percentage'],
                'average_time_seconds' => $section['average_time_seconds'],
            ])->all(),
            'performance_graph' => [
                'score_max' => max((float) $trend->max('score'), 1),
                'accuracy_max' => 100,
                'points' => $trend->all(),
            ],
        ];
    }

    public function attemptReport(UserExam $attempt): array
    {
        $analytics = $attempt->analytics ?? [];
        $weakAreas = collect($analytics['section_breakdown'] ?? [])
            ->sortBy('accuracy_percentage')
            ->take(2)
            ->values()
            ->all();
        $reviewSections = $attempt->exam->sections->map(function ($section) use ($attempt) {
            $assignedIds = collect($attempt->assigned_question_ids[$section->id] ?? []);
            $questions = $section->questions
                ->whereIn('id', $assignedIds->isEmpty() ? $section->questions->pluck('id') : $assignedIds)
                ->sortBy(fn ($question) => $assignedIds->search($question->id))
                ->values()
                ->map(function ($question) use ($attempt) {
                    $answer = $attempt->answers->firstWhere('question_id', $question->id);
                    $selectedAnswer = $answer?->selected_answer;
                    $correctAnswer = $question->correct_answer;
                    $isCorrect = (bool) ($answer?->is_correct ?? false);
                    $explanation = $question->explanation ?: 'Review the prompt carefully, eliminate distractors, and anchor your choice to the clearest evidence in the stem or passage.';
                    $mistakeAdvice = $isCorrect
                        ? 'Good answer. Use the same evidence-first approach on similar questions.'
                        : "You chose '{$selectedAnswer}'. The correct answer is '{$correctAnswer}'. Slow down, compare each option against the passage or stem, and eliminate answers that only feel plausible instead of being directly supported.";

                    return [
                        'id' => $question->id,
                        'stem' => $question->stem,
                        'passage' => $question->passage,
                        'options' => $question->options ?? [],
                        'selected_answer' => $selectedAnswer,
                        'correct_answer' => $correctAnswer,
                        'is_correct' => $isCorrect,
                        'time_spent_seconds' => $answer?->time_spent_seconds ?? 0,
                        'explanation' => $explanation,
                        'mistake_advice' => $mistakeAdvice,
                    ];
                });

            return [
                'section' => $section->name,
                'questions' => $questions->all(),
            ];
        })->all();

        return [
            'summary' => $analytics,
            'weak_topics' => $weakAreas,
            'suggestions' => array_map(
                fn (array $section): string => "Focus on {$section['section']} drills and timed repetition to improve decision speed.",
                $weakAreas
            ),
            'review_sections' => $reviewSections,
        ];
    }

    protected function weakSections($attempts): array
    {
        $sectionScores = [];

        foreach ($attempts as $attempt) {
            foreach (($attempt->analytics['section_breakdown'] ?? []) as $section) {
                $sectionScores[$section['section']][] = $section['accuracy_percentage'];
            }
        }

        return collect($sectionScores)
            ->map(fn (array $scores, string $section) => [
                'section' => $section,
                'accuracy_percentage' => round(collect($scores)->avg(), 2),
            ])
            ->sortBy('accuracy_percentage')
            ->take(3)
            ->values()
            ->all();
    }
}
