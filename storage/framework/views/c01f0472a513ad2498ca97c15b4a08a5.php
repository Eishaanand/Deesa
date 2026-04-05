<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Attempt Report</p>
            <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950"><?php echo e($attempt->exam->title); ?></h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($attempt->exam->sequence_number ?? 0) >= 3 && ! auth()->user()->hasActivePremium()): ?>
                <div class="mt-6 rounded-3xl bg-amber-50 px-5 py-4 text-sm text-amber-700">
                    You have completed the first three mocks. Continue with premium access for GBP <?php echo e($subscriptionPrice); ?>/month.
                    <a href="<?php echo e(route('subscription.show')); ?>" class="ml-2 font-semibold underline">Upgrade now</a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Score</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950"><?php echo e($report['summary']['total_score'] ?? 0); ?></p>
                    <p class="mt-2 text-sm text-slate-500">Out of <?php echo e($report['summary']['total_questions'] ?? 0); ?> marks</p>
                </div>
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Accuracy</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950"><?php echo e($report['summary']['accuracy_percentage'] ?? 0); ?>%</p>
                </div>
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Correct</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950"><?php echo e($report['summary']['correct_answers'] ?? 0); ?></p>
                </div>
                <div class="glass-card-strong rounded-3xl p-4">
                    <p class="text-sm text-slate-500">Incorrect</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950"><?php echo e($report['summary']['incorrect_answers'] ?? 0); ?></p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Section Breakdown</h2>
                <div class="mt-6 space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($report['summary']['section_breakdown'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="glass-card-strong rounded-3xl p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900"><?php echo e($section['section']); ?></p>
                                    <p class="text-sm text-slate-500"><?php echo e($section['score']); ?>/<?php echo e($section['total_questions']); ?> marks · <?php echo e($section['average_time_seconds']); ?> sec average per question</p>
                                </div>
                                <p class="text-lg font-semibold text-slate-900"><?php echo e($section['accuracy_percentage']); ?>%</p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="space-y-6">
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Weak Topics</h2>
                    <div class="mt-5 space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $report['weak_topics']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-3xl bg-slate-950 px-4 py-4 text-white">
                                <p class="font-semibold"><?php echo e($topic['section']); ?></p>
                                <p class="mt-1 text-sm text-slate-300"><?php echo e($topic['accuracy_percentage']); ?>% accuracy</p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="glass-card rounded-[28px] p-6">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">AI Suggestions</h2>
                    <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-600">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $report['suggestions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($suggestion); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! auth()->user()->hasActivePremium()): ?>
                        <div class="mt-6">
                            <a href="<?php echo e(route('subscription.show')); ?>" class="btn-secondary">Premium GBP <?php echo e($subscriptionPrice); ?>/month</a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="glass-card rounded-[28px] p-6">
            <h2 class="font-display text-2xl font-semibold text-slate-900">Question Review</h2>
            <div class="mt-6 space-y-8">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $report['review_sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="font-display text-xl font-semibold text-slate-900"><?php echo e($section['section']); ?></h3>
                            <span class="text-sm text-slate-500"><?php echo e(count($section['questions'])); ?> questions</span>
                        </div>

                        <div class="space-y-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $section['questions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="glass-card-strong rounded-[24px] p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">
                                            Question <?php echo e($index + 1); ?>

                                        </p>
                                        <span class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] <?php echo e($question['is_correct'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'); ?>">
                                            <?php echo e($question['is_correct'] ? 'Correct' : 'Incorrect'); ?>

                                        </span>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($question['passage']): ?>
                                        <div class="mt-4 rounded-3xl bg-white/70 p-5 text-sm leading-7 text-slate-600">
                                            <?php echo e($question['passage']); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <h4 class="mt-5 text-lg font-semibold leading-8 text-slate-900"><?php echo e($question['stem']); ?></h4>

                                    <div class="mt-5 space-y-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $question['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $isSelected = $question['selected_answer'] === $option;
                                                $isCorrectOption = $question['correct_answer'] === $option;
                                                $optionClass = $isCorrectOption
                                                    ? 'border-emerald-300 bg-emerald-50 text-emerald-800'
                                                    : ($isSelected && ! $question['is_correct']
                                                        ? 'border-red-300 bg-red-50 text-red-800'
                                                        : 'border-slate-200 bg-white/70 text-slate-700');
                                            ?>
                                            <div class="rounded-3xl border px-4 py-4 text-sm <?php echo e($optionClass); ?>">
                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                    <span><?php echo e($option); ?></span>
                                                    <div class="flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.2em]">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCorrectOption): ?>
                                                            <span>Correct answer</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSelected): ?>
                                                            <span>Your answer</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <div class="mt-5 grid gap-4 lg:grid-cols-[0.9fr_1.1fr]">
                                        <div class="rounded-3xl bg-slate-100 px-4 py-4 text-sm text-slate-600">
                                            <p class="font-semibold text-slate-900">Answer Summary</p>
                                            <p class="mt-2">Your answer: <?php echo e($question['selected_answer'] ?? 'Not answered'); ?></p>
                                            <p class="mt-1">Correct answer: <?php echo e($question['correct_answer']); ?></p>
                                            <p class="mt-1">Time spent: <?php echo e($question['time_spent_seconds']); ?> seconds</p>
                                        </div>
                                        <div class="rounded-3xl bg-sky-50 px-4 py-4 text-sm leading-7 text-slate-700">
                                            <p class="font-semibold text-slate-900">Explanation</p>
                                            <p class="mt-2"><?php echo e($question['explanation']); ?></p>
                                            <p class="mt-3 font-semibold text-slate-900">How to avoid the same mistake</p>
                                            <p class="mt-2"><?php echo e($question['mistake_advice']); ?></p>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/results/show.blade.php ENDPATH**/ ?>