<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Section;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserExam;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExamEngineService
{
    public function __construct(
        protected GeminiQuestionGeneratorService $generator,
    ) {
    }

    public function startAttempt(User $user, Exam $exam): UserExam
    {
        $exam->loadMissing('sections.questions');
        $firstSection = $exam->sections->sortBy('sequence')->first();
        $assignedQuestionIds = [];

        foreach ($exam->sections as $section) {
            if ($section->questions()->count() === 0) {
                $this->generator->ensureQuestionBank($section, $this->generator->questionCountForSection($section));
                $section->load('questions');
            }

            $assignedQuestionIds[$section->id] = $this->questionIdsForAttempt($section, $this->generator->questionCountForSection($section));
        }

        $existing = UserExam::query()
            ->where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['in_progress', 'paused'])
            ->latest()
            ->first();

        if ($existing) {
            return $existing->fresh(['exam.sections.questions', 'answers']);
        }

        return UserExam::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'status' => 'in_progress',
            'current_section_id' => $firstSection?->id,
            'current_question_index' => 0,
            'section_seconds_remaining' => $firstSection?->time_limit_seconds ?? 0,
            'started_at' => now(),
            'analytics' => [],
            'assigned_question_ids' => $assignedQuestionIds,
        ]);
    }

    public function saveAnswer(UserExam $attempt, array $payload): UserAnswer
    {
        abort_if(in_array($attempt->status, ['paused', 'submitted', 'ended'], true), 422, 'Attempt is not active.');

        $question = Question::findOrFail($payload['question_id']);
        $currentSectionId = $attempt->current_section_id;

        abort_unless((int) $payload['section_id'] === (int) $currentSectionId, 422, 'Cannot answer outside active section.');

        return UserAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ],
            [
                'section_id' => $payload['section_id'],
                'selected_answer' => $payload['selected_answer'],
                'is_correct' => $payload['selected_answer'] === $question->correct_answer,
                'time_spent_seconds' => $payload['time_spent_seconds'],
                'answered_at' => now(),
                'meta' => [],
            ]
        );
    }

    public function submitAttempt(UserExam $attempt): UserExam
    {
        $attempt->loadMissing('exam.sections.questions', 'answers');

        $report = $this->buildAttemptAnalytics($attempt);

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'current_section_id' => null,
            'current_question_index' => 0,
            'section_seconds_remaining' => 0,
            'total_score' => $report['total_score'],
            'accuracy_percentage' => $report['accuracy_percentage'],
            'analytics' => $report,
        ]);

        return $attempt->fresh(['answers', 'exam.sections.questions']);
    }

    public function pauseAttempt(UserExam $attempt): void
    {
        $attempt->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);
    }

    public function resumeAttempt(UserExam $attempt): void
    {
        $attempt->update([
            'status' => 'in_progress',
            'paused_at' => null,
        ]);
    }

    public function endAttempt(UserExam $attempt): void
    {
        $report = $this->buildAttemptAnalytics($attempt->fresh(['exam.sections.questions', 'answers']));

        $attempt->update([
            'status' => 'ended',
            'ended_at' => now(),
            'current_section_id' => null,
            'current_question_index' => 0,
            'section_seconds_remaining' => 0,
            'total_score' => $report['total_score'],
            'accuracy_percentage' => $report['accuracy_percentage'],
            'analytics' => $report,
        ]);
    }

    public function syncAttemptProgress(UserExam $attempt, int $questionIndex, int $sectionSecondsRemaining): void
    {
        $attempt->update([
            'current_question_index' => $questionIndex,
            'section_seconds_remaining' => $sectionSecondsRemaining,
        ]);
    }

    public function syncAttemptRuntime(
        UserExam $attempt,
        int $questionIndex,
        int $sectionSecondsRemaining,
        int $currentSectionId,
        array $sectionTimers,
        array $sectionIndexes
    ): void {
        $analytics = $attempt->analytics ?? [];
        $analytics['runtime'] = [
            'section_timers' => $sectionTimers,
            'section_indexes' => $sectionIndexes,
        ];

        $attempt->update([
            'current_section_id' => $currentSectionId,
            'current_question_index' => $questionIndex,
            'section_seconds_remaining' => $sectionSecondsRemaining,
            'analytics' => $analytics,
        ]);
    }

    public function buildAttemptAnalytics(UserExam $attempt): array
    {
        $sections = $attempt->exam->sections;
        $answers = $attempt->answers->keyBy('question_id');
        $allQuestions = $sections->flatMap(function (Section $section) use ($attempt): Collection {
            return $this->questionsForAttemptSection($attempt, $section);
        });

        $correct = 0;
        $total = max($allQuestions->count(), 1);
        $sectionBreakdown = [];

        foreach ($sections as $section) {
            $sectionQuestions = $this->questionsForAttemptSection($attempt, $section);
            $sectionCorrect = 0;
            $totalTime = 0;

            foreach ($sectionQuestions as $question) {
                $answer = $answers->get($question->id);
                $sectionCorrect += (int) ($answer?->is_correct ?? false);
                $totalTime += (int) ($answer?->time_spent_seconds ?? 0);
            }

            $correct += $sectionCorrect;

            $sectionBreakdown[] = [
                'section' => $section->name,
                'type' => $section->type,
                'score' => $sectionCorrect,
                'total_questions' => $sectionQuestions->count(),
                'accuracy_percentage' => $sectionQuestions->count() > 0 ? round(($sectionCorrect / $sectionQuestions->count()) * 100, 2) : 0,
                'average_time_seconds' => $sectionQuestions->count() > 0 ? round($totalTime / $sectionQuestions->count(), 2) : 0,
            ];
        }

        return [
            'total_score' => $correct,
            'total_questions' => $allQuestions->count(),
            'accuracy_percentage' => round(($correct / $total) * 100, 2),
            'correct_answers' => $correct,
            'incorrect_answers' => max($allQuestions->count() - $correct, 0),
            'section_breakdown' => $sectionBreakdown,
        ];
    }

    protected function questionIdsForAttempt(Section $section, int $count): array
    {
        $query = $section->questions();

        if ($this->generator->hasApiKey()) {
            $geminiIds = (clone $query)
                ->where('source', 'gemini')
                ->orderByDesc('id')
                ->limit($count)
                ->pluck('id')
                ->reverse()
                ->values()
                ->all();

            if (count($geminiIds) === $count) {
                return $geminiIds;
            }
        }

        return $query->orderBy('sequence')->limit($count)->pluck('id')->all();
    }

    protected function questionsForAttemptSection(UserExam $attempt, Section $section): Collection
    {
        $assignedIds = collect($attempt->assigned_question_ids[$section->id] ?? []);

        if ($assignedIds->isEmpty()) {
            return $section->questions;
        }

        return $section->questions
            ->whereIn('id', $assignedIds)
            ->sortBy(fn (Question $question) => $assignedIds->search($question->id))
            ->values();
    }
}
