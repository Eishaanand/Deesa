<div wire:poll.1s="tick" class="space-y-6">
    <section class="glass-card rounded-[28px] p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Timed Exam</p>
                <h1 class="mt-2 font-display text-3xl font-semibold text-slate-950"><?php echo e($this->currentSection()?->name); ?></h1>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="rounded-full bg-slate-950 px-5 py-3 text-lg font-semibold text-white">
                    <?php echo e(gmdate('i:s', $sectionSecondsRemaining)); ?>

                </div>
                <button wire:click="pause" class="btn-secondary" type="button">
                    <?php echo e($attempt->status === 'paused' ? 'Paused' : 'Pause Exam'); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attempt->status === 'paused'): ?>
                    <button wire:click="resumeAttempt" class="btn-primary" type="button">Resume Exam</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="endExam" class="btn-secondary" type="button">End Exam</button>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-4">
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Answered</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950"><?php echo e($this->liveStats['answered']); ?></p>
        </div>
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Remaining</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950"><?php echo e($this->liveStats['remaining']); ?></p>
        </div>
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Live Accuracy</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950"><?php echo e($this->liveStats['accuracy']); ?>%</p>
        </div>
        <div class="glass-card rounded-[24px] p-5">
            <p class="text-sm text-slate-500">Time Spent</p>
            <p class="mt-1 text-2xl font-semibold text-slate-950"><?php echo e(gmdate('H:i:s', $this->liveStats['time_spent'])); ?></p>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-[260px_minmax(0,1fr)_280px] xl:items-start">
        <div class="glass-card rounded-[28px] p-4 xl:sticky xl:top-6 xl:max-h-[calc(100vh-15rem)] xl:overflow-y-auto">
            <h2 class="text-base font-semibold text-slate-900">Sections</h2>
            <div class="mt-3 space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $attempt->exam->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isActiveSection = $section->id === $this->currentSection()?->id;
                        $sectionQuestions = $section->questions
                            ->whereIn('id', $attempt->assigned_question_ids[$section->id] ?? $section->questions->pluck('id')->all())
                            ->values();
                        $answeredCount = $sectionQuestions->filter(fn ($question) => filled($selectedAnswers[$question->id] ?? null))->count();
                        $sectionTimeSpent = $sectionQuestions->sum(fn ($question) => (int) ($timeSpent[$question->id] ?? 0));
                        $sectionAveragePace = $answeredCount > 0 ? round($sectionTimeSpent / $answeredCount, 1) : 0;
                    ?>
                    <button
                        wire:click="goToSection(<?php echo e($section->id); ?>)"
                        type="button"
                        class="w-full rounded-[22px] border px-4 py-3 text-left transition <?php echo e($isActiveSection ? 'border-sky-400 bg-sky-100 text-sky-900' : 'border-slate-200 bg-white/80 text-slate-700'); ?>"
                        <?php if($attempt->status === 'paused'): echo 'disabled'; endif; ?>
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-semibold"><?php echo e($section->name); ?></span>
                            <span class="text-xs"><?php echo e(gmdate('i:s', $sectionTimers[$section->id] ?? $section->time_limit_seconds)); ?></span>
                        </div>
                        <p class="mt-2 text-xs opacity-80"><?php echo e($answeredCount); ?>/<?php echo e($sectionQuestions->count()); ?> attempted</p>
                        <p class="mt-1 text-xs opacity-80">Avg pace <?php echo e($sectionAveragePace); ?>s · Spent <?php echo e(gmdate('i:s', $sectionTimeSpent)); ?></p>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->currentQuestion()): ?>
            <section wire:key="question-shell-<?php echo e($this->currentQuestion()->id); ?>" class="glass-card rounded-[28px] p-6">
                <p class="text-sm text-slate-500">Question <?php echo e($questionIndex + 1); ?> of <?php echo e($this->questions->count()); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attempt->status === 'paused'): ?>
                    <div class="mt-4 rounded-3xl bg-amber-50 px-4 py-4 text-sm text-amber-700">
                        This attempt is paused. Resume to continue the timer and save more answers.
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->currentQuestion()->passage): ?>
                    <div class="mt-4 max-h-44 overflow-y-auto rounded-3xl bg-white/70 p-5 text-sm leading-7 text-slate-600">
                        <?php echo e($this->currentQuestion()->passage); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h2 class="mt-5 text-lg font-semibold leading-8 text-slate-900"><?php echo e($this->currentQuestion()->stem); ?></h2>

                <div class="mt-5 space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->currentQuestion()->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label wire:key="question-<?php echo e($this->currentQuestion()->id); ?>-option-<?php echo e(md5($option)); ?>" class="glass-card-strong flex cursor-pointer items-center gap-3 rounded-3xl px-4 py-3.5">
                            <input
                                id="question-<?php echo e($this->currentQuestion()->id); ?>-option-<?php echo e($loop->index); ?>"
                                name="question_<?php echo e($this->currentQuestion()->id); ?>"
                                type="radio"
                                wire:model.live="selectedAnswers.<?php echo e($this->currentQuestion()->id); ?>"
                                value="<?php echo e($option); ?>"
                                <?php if($attempt->status === 'paused'): echo 'disabled'; endif; ?>
                            >
                            <span class="text-sm text-slate-700"><?php echo e($option); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="mt-6 flex justify-between">
                    <button wire:click="$set('questionIndex', <?php echo e(max($questionIndex - 1, 0)); ?>)" class="btn-secondary" type="button" <?php if($questionIndex === 0 || $attempt->status === 'paused'): echo 'disabled'; endif; ?>>
                        Previous
                    </button>
                    <button wire:click="saveAndNext" class="btn-primary" type="button">
                        <?php echo e($questionIndex + 1 < $this->questions->count() ? 'Save & Next' : 'Finish Section'); ?>

                    </button>
                </div>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="glass-card rounded-[28px] p-4 xl:sticky xl:top-6 xl:max-h-[calc(100vh-15rem)] xl:overflow-y-auto">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-slate-900">Question Palette</h2>
            </div>
            <p class="mt-2 text-xs text-slate-500">Blue = attempted, white = blank</p>
            <div class="mt-4 grid grid-cols-4 gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isAnswered = filled($selectedAnswers[$question->id] ?? null);
                        $isActiveQuestion = $this->currentQuestion()?->id === $question->id;
                    ?>
                    <button
                        wire:click="goToQuestion(<?php echo e($index); ?>)"
                        type="button"
                        class="h-10 rounded-2xl border text-sm font-semibold transition <?php echo e($isActiveQuestion ? 'border-slate-950 bg-slate-950 text-white' : ($isAnswered ? 'border-sky-300 bg-sky-100 text-sky-900' : 'border-slate-200 bg-white text-slate-700')); ?>"
                        <?php if($attempt->status === 'paused'): echo 'disabled'; endif; ?>
                    >
                        <?php echo e($index + 1); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php /**PATH /var/www/html/resources/views/livewire/exam-runner.blade.php ENDPATH**/ ?>