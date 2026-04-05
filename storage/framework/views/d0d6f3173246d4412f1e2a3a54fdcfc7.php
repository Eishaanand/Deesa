<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Explore Exams</p>
            <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Practice full UCAT-style timed exams.</h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! auth()->user()->hasActivePremium()): ?>
                <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">
                    The first three mocks unlock one by one. Additional mocks require premium access at GBP 30 per month.
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $examCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($exam = $card['exam']); ?>
                <article class="glass-card rounded-[28px] p-6">
                    <p class="text-sm uppercase tracking-[0.25em] text-slate-500"><?php echo e($exam->status); ?></p>
                    <h2 class="mt-3 font-display text-2xl font-semibold text-slate-900"><?php echo e($exam->title); ?></h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600"><?php echo e($exam->description); ?></p>
                    <div class="mt-5 flex items-center justify-between text-sm text-slate-500">
                        <span>Mock <?php echo e($exam->sequence_number); ?> · <?php echo e($exam->sections->count()); ?> sections</span>
                        <span><?php echo e(round($exam->total_duration_seconds / 60)); ?> minutes</span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($card['subscription_required']): ?>
                        <div class="mt-4 rounded-3xl bg-amber-50 px-4 py-4 text-sm text-amber-700">
                            Premium required after the first three mocks. Upgrade for GBP 30/month.
                        </div>
                    <?php elseif(! $card['unlocked']): ?>
                        <div class="mt-4 rounded-3xl bg-slate-100 px-4 py-4 text-sm text-slate-600">
                            Complete the previous mock to unlock this exam automatically.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="mt-6 flex gap-3">
                        <a href="<?php echo e(route('exams.show', $exam)); ?>" class="btn-secondary">View</a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($card['can_start']): ?>
                            <form method="POST" action="<?php echo e(route('exams.start', $exam)); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="btn-primary" type="submit">Start Exam</button>
                            </form>
                        <?php elseif($card['subscription_required']): ?>
                            <a href="<?php echo e(route('subscription.show')); ?>" class="btn-primary">Go Premium</a>
                        <?php else: ?>
                            <button class="btn-primary opacity-60" type="button" disabled>Locked</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/exams/index.blade.php ENDPATH**/ ?>