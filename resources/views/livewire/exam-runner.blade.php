<div wire:poll.1s="tick" class="space-y-6">
    <section class="glass-card rounded-[28px] p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Timed Exam</p>
                <h1 class="mt-2 font-display text-3xl font-semibold text-slate-950">{{ $this->currentSection()?->name }}</h1>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="rounded-full bg-slate-950 px-5 py-3 text-lg font-semibold text-white">
                    {{ gmdate('i:s', $sectionSecondsRemaining) }}
                </div>
                <button wire:click="pause" class="btn-secondary" type="button">
                    {{ $attempt->status === 'paused' ? 'Paused' : 'Pause Exam' }}
                </button>
                @if ($attempt->status === 'paused')
                    <button wire:click="resumeAttempt" class="btn-primary" type="button">Resume Exam</button>
                @endif
                <button wire:click="endExam" class="btn-secondary" type="button">End Exam</button>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-4">
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Answered</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950">{{ $this->liveStats['answered'] }}</p>
        </div>
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Remaining</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950">{{ $this->liveStats['remaining'] }}</p>
        </div>
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Live Accuracy</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950">{{ $this->liveStats['accuracy'] }}%</p>
        </div>
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Time Spent</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950">{{ gmdate('H:i:s', $this->liveStats['time_spent']) }}</p>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-[260px_minmax(0,1fr)_280px] xl:items-start">
        <div class="glass-card rounded-[28px] p-4 xl:sticky xl:top-6 xl:max-h-[calc(100vh-15rem)] xl:overflow-y-auto">
            <h2 class="text-base font-semibold text-slate-900">Sections</h2>
            <div class="mt-3 space-y-2">
                @foreach ($attempt->exam->sections as $section)
                    @php
                        $isActiveSection = $section->id === $this->currentSection()?->id;
                        $sectionQuestions = $section->questions
                            ->whereIn('id', $attempt->assigned_question_ids[$section->id] ?? $section->questions->pluck('id')->all())
                            ->values();
                        $answeredCount = $sectionQuestions->filter(fn ($question) => filled($selectedAnswers[$question->id] ?? null))->count();
                        $sectionTimeSpent = $sectionQuestions->sum(fn ($question) => (int) ($timeSpent[$question->id] ?? 0));
                        $sectionAveragePace = $answeredCount > 0 ? round($sectionTimeSpent / $answeredCount, 1) : 0;
                    @endphp
                    <button
                        wire:click="goToSection({{ $section->id }})"
                        type="button"
                        class="w-full rounded-[22px] border px-4 py-3 text-left transition {{ $isActiveSection ? 'border-sky-400 bg-sky-100 text-sky-900' : 'border-slate-200 bg-white/80 text-slate-700' }}"
                        @disabled($attempt->status === 'paused')
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-semibold">{{ $section->name }}</span>
                            <span class="text-xs">{{ gmdate('i:s', $sectionTimers[$section->id] ?? $section->time_limit_seconds) }}</span>
                        </div>
                        <p class="mt-2 text-xs opacity-80">{{ $answeredCount }}/{{ $sectionQuestions->count() }} attempted</p>
                        <p class="mt-1 text-xs opacity-80">Avg pace {{ $sectionAveragePace }}s · Spent {{ gmdate('i:s', $sectionTimeSpent) }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        @if ($this->currentQuestion())
            <section wire:key="question-shell-{{ $this->currentQuestion()->id }}" class="glass-card rounded-[28px] p-6">
                <p class="text-sm text-slate-500">Question {{ $questionIndex + 1 }} of {{ $this->questions->count() }}</p>
                @if ($attempt->status === 'paused')
                    <div class="mt-4 rounded-3xl bg-amber-50 px-4 py-4 text-sm text-amber-700">
                        This attempt is paused. Resume to continue the timer and save more answers.
                    </div>
                @endif
                @if ($this->currentQuestion()->passage)
                    <div class="mt-4 max-h-44 overflow-y-auto rounded-3xl bg-white/70 p-5 text-sm leading-7 text-slate-600">
                        {{ $this->currentQuestion()->passage }}
                    </div>
                @endif
                <h2 class="mt-5 text-lg font-semibold leading-8 text-slate-900">{{ $this->currentQuestion()->stem }}</h2>

                <div class="mt-5 space-y-3">
                    @foreach ($this->currentQuestion()->options as $option)
                        <label wire:key="question-{{ $this->currentQuestion()->id }}-option-{{ md5($option) }}" class="glass-card-strong flex cursor-pointer items-center gap-3 rounded-3xl px-4 py-3.5">
                            <input
                                id="question-{{ $this->currentQuestion()->id }}-option-{{ $loop->index }}"
                                name="question_{{ $this->currentQuestion()->id }}"
                                type="radio"
                                wire:model.live="selectedAnswers.{{ $this->currentQuestion()->id }}"
                                value="{{ $option }}"
                                @disabled($attempt->status === 'paused')
                            >
                            <span class="text-sm text-slate-700">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-between">
                    <button wire:click="$set('questionIndex', {{ max($questionIndex - 1, 0) }})" class="btn-secondary" type="button" @disabled($questionIndex === 0 || $attempt->status === 'paused')>
                        Previous
                    </button>
                    <button wire:click="saveAndNext" class="btn-primary" type="button">
                        {{ $questionIndex + 1 < $this->questions->count() ? 'Save & Next' : 'Finish Section' }}
                    </button>
                </div>
            </section>
        @endif

        <div class="glass-card rounded-[28px] p-4 xl:sticky xl:top-6 xl:max-h-[calc(100vh-15rem)] xl:overflow-y-auto">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-slate-900">Question Palette</h2>
            </div>
            <p class="mt-2 text-xs text-slate-500">Blue = attempted, white = blank</p>
            <div class="mt-4 grid grid-cols-4 gap-2">
                @foreach ($this->questions as $index => $question)
                    @php
                        $isAnswered = filled($selectedAnswers[$question->id] ?? null);
                        $isActiveQuestion = $this->currentQuestion()?->id === $question->id;
                    @endphp
                    <button
                        wire:click="goToQuestion({{ $index }})"
                        type="button"
                        class="h-10 rounded-2xl border text-sm font-semibold transition {{ $isActiveQuestion ? 'border-slate-950 bg-slate-950 text-white' : ($isAnswered ? 'border-sky-300 bg-sky-100 text-sky-900' : 'border-slate-200 bg-white text-slate-700') }}"
                        @disabled($attempt->status === 'paused')
                    >
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>
</div>
