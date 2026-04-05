<?php

namespace App\Livewire;

use App\Models\Question;
use App\Models\Section;
use App\Models\UserExam;
use App\Services\ExamEngineService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ExamRunner extends Component
{
    public UserExam $attempt;
    public int $questionIndex = 0;
    public int $sectionSecondsRemaining = 0;
    public array $sectionTimers = [];
    public array $sectionIndexes = [];
    public array $selectedAnswers = [];
    public array $timeSpent = [];
    public bool $showEndConfirm = false;

    public function mount(UserExam $attempt): void
    {
        $this->attempt = $attempt->load('exam.sections.questions', 'answers');
        $this->selectedAnswers = $attempt->answers->pluck('selected_answer', 'question_id')->all();
        $this->timeSpent = $attempt->answers->pluck('time_spent_seconds', 'question_id')->all();
        $this->questionIndex = $attempt->current_question_index;

        $runtime = $attempt->analytics['runtime'] ?? [];
        $storedTimers = $runtime['section_timers'] ?? [];
        $storedIndexes = $runtime['section_indexes'] ?? [];

        foreach ($this->attempt->exam->sections as $section) {
            $this->sectionTimers[$section->id] = (int) ($storedTimers[$section->id] ?? $section->time_limit_seconds);
            $this->sectionIndexes[$section->id] = (int) ($storedIndexes[$section->id] ?? 0);
        }

        $currentSectionId = $this->attempt->current_section_id ?: $this->attempt->exam->sections->first()?->id;
        $this->questionIndex = (int) ($this->sectionIndexes[$currentSectionId] ?? $this->questionIndex);
        $this->sectionSecondsRemaining = (int) ($this->sectionTimers[$currentSectionId] ?? ($this->currentSection()?->time_limit_seconds ?? 0));
    }

    #[Computed]
    public function currentSection(): ?Section
    {
        return $this->attempt->exam->sections->firstWhere('id', $this->attempt->current_section_id);
    }

    #[Computed]
    public function questions()
    {
        $section = $this->currentSection();

        if (! $section) {
            return collect();
        }

        $assignedIds = collect($this->attempt->assigned_question_ids[$section->id] ?? []);

        if ($assignedIds->isEmpty()) {
            return $section->questions ?? collect();
        }

        return $section->questions
            ->whereIn('id', $assignedIds)
            ->sortBy(fn (Question $question) => $assignedIds->search($question->id))
            ->values();
    }

    #[Computed]
    public function currentQuestion(): ?Question
    {
        return $this->questions[$this->questionIndex] ?? null;
    }

    public function saveAndNext(ExamEngineService $engine): void
    {
        if ($this->attempt->status === 'paused') {
            return;
        }

        $question = $this->currentQuestion();

        if ($question) {
            $engine->saveAnswer($this->attempt, [
                'question_id' => $question->id,
                'section_id' => $question->section_id,
                'selected_answer' => $this->selectedAnswers[$question->id] ?? null,
                'time_spent_seconds' => $this->timeSpent[$question->id] ?? 0,
            ]);
        }

        if ($this->questionIndex < ($this->questions->count() - 1)) {
            $this->questionIndex++;
            $this->persistRuntimeState($engine);

            return;
        }

        $sections = $this->attempt->exam->sections->values();
        $currentSectionIndex = $sections->search(fn (Section $section) => $section->id === $this->attempt->current_section_id);
        $nextSection = $sections[$currentSectionIndex + 1] ?? null;

        if ($nextSection) {
            $this->goToSection($nextSection->id, $engine);

            return;
        }

        $this->persistRuntimeState($engine);
        $engine->submitAttempt($this->attempt);
        $this->redirectRoute('results.show', $this->attempt);
    }

    public function tick(): void
    {
        if ($this->attempt->status !== 'in_progress') {
            return;
        }

        $this->sectionSecondsRemaining = max($this->sectionSecondsRemaining - 1, 0);
        if ($this->currentSection()) {
            $this->sectionTimers[$this->currentSection()->id] = $this->sectionSecondsRemaining;
        }

        if ($this->currentQuestion()) {
            $questionId = $this->currentQuestion()->id;
            $this->timeSpent[$questionId] = ($this->timeSpent[$questionId] ?? 0) + 1;
        }

        if ($this->currentSection()) {
            app(ExamEngineService::class)->syncAttemptRuntime(
                $this->attempt,
                $this->questionIndex,
                $this->sectionSecondsRemaining,
                $this->currentSection()->id,
                $this->sectionTimers,
                $this->sectionIndexes,
            );
        }

        if ($this->sectionSecondsRemaining === 0) {
            $this->saveAndNext(app(ExamEngineService::class));
        }
    }

    public function pause(ExamEngineService $engine): void
    {
        $this->persistRuntimeState($engine);
        $engine->pauseAttempt($this->attempt);
        $this->attempt->refresh();
    }

    public function resumeAttempt(ExamEngineService $engine): void
    {
        $engine->resumeAttempt($this->attempt);
        $this->attempt->refresh();
    }

    public function endExam(ExamEngineService $engine): void
    {
        $question = $this->currentQuestion();

        if ($question) {
            $engine->saveAnswer($this->attempt, [
                'question_id' => $question->id,
                'section_id' => $question->section_id,
                'selected_answer' => $this->selectedAnswers[$question->id] ?? null,
                'time_spent_seconds' => $this->timeSpent[$question->id] ?? 0,
            ]);
        }

        $this->persistRuntimeState($engine);
        $engine->endAttempt($this->attempt);
        $this->redirectRoute('results.show', $this->attempt);
    }

    public function goToSection(int $sectionId, ExamEngineService $engine): void
    {
        if ($this->attempt->status === 'paused') {
            return;
        }

        $currentQuestion = $this->currentQuestion();

        if ($currentQuestion) {
            $engine->saveAnswer($this->attempt, [
                'question_id' => $currentQuestion->id,
                'section_id' => $currentQuestion->section_id,
                'selected_answer' => $this->selectedAnswers[$currentQuestion->id] ?? null,
                'time_spent_seconds' => $this->timeSpent[$currentQuestion->id] ?? 0,
            ]);
        }

        if (! $this->attempt->exam->sections->firstWhere('id', $sectionId)) {
            return;
        }

        if ($this->currentSection()) {
            $this->sectionIndexes[$this->currentSection()->id] = $this->questionIndex;
            $this->sectionTimers[$this->currentSection()->id] = $this->sectionSecondsRemaining;
        }

        $this->attempt->current_section_id = $sectionId;
        $this->questionIndex = (int) ($this->sectionIndexes[$sectionId] ?? 0);
        $this->sectionSecondsRemaining = (int) ($this->sectionTimers[$sectionId] ?? $this->attempt->exam->sections->firstWhere('id', $sectionId)?->time_limit_seconds ?? 0);

        $this->persistRuntimeState($engine);
    }

    public function goToQuestion(int $index, ExamEngineService $engine): void
    {
        if ($this->attempt->status === 'paused') {
            return;
        }

        if ($index < 0 || $index >= $this->questions->count()) {
            return;
        }

        $currentQuestion = $this->currentQuestion();

        if ($currentQuestion) {
            $engine->saveAnswer($this->attempt, [
                'question_id' => $currentQuestion->id,
                'section_id' => $currentQuestion->section_id,
                'selected_answer' => $this->selectedAnswers[$currentQuestion->id] ?? null,
                'time_spent_seconds' => $this->timeSpent[$currentQuestion->id] ?? 0,
            ]);
        }

        $this->questionIndex = $index;
        if ($this->currentSection()) {
            $this->sectionIndexes[$this->currentSection()->id] = $this->questionIndex;
        }

        $this->persistRuntimeState($engine);
    }

    #[Computed]
    public function liveStats(): array
    {
        $answered = collect($this->selectedAnswers)->filter()->count();
        $correct = $this->questions
            ->filter(fn (Question $question) => ($this->selectedAnswers[$question->id] ?? null) === $question->correct_answer)
            ->count();
        $timeSpentTotal = array_sum($this->timeSpent);

        return [
            'answered' => $answered,
            'remaining' => max($this->questions->count() - $answered, 0),
            'accuracy' => $answered > 0 ? round(($correct / $answered) * 100, 1) : 0,
            'time_spent' => $timeSpentTotal,
        ];
    }

    public function render()
    {
        return view('livewire.exam-runner');
    }

    protected function persistRuntimeState(ExamEngineService $engine): void
    {
        if (! $this->currentSection()) {
            return;
        }

        $sectionId = $this->currentSection()->id;
        $this->sectionIndexes[$sectionId] = $this->questionIndex;
        $this->sectionTimers[$sectionId] = $this->sectionSecondsRemaining;

        $engine->syncAttemptRuntime(
            $this->attempt,
            $this->questionIndex,
            $this->sectionSecondsRemaining,
            $sectionId,
            $this->sectionTimers,
            $this->sectionIndexes,
        );

        $this->attempt->refresh();
    }
}
