<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Exam Management</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Create, edit, and expand AI-enabled UCAT exams.</h1>
                </div>
                <a href="<?php echo e(route('admin.exams.create')); ?>" class="btn-primary">New Exam</a>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <section class="glass-card rounded-[24px] px-5 py-4 text-sm text-slate-700">
                <?php echo e(session('status')); ?>

            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="glass-card rounded-[28px] p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="font-display text-2xl font-semibold text-slate-900"><?php echo e($exam->title); ?></h2>
                            <p class="mt-2 text-sm text-slate-500"><?php echo e($exam->sections_count); ?> sections · <?php echo e($exam->status); ?></p>
                        </div>
                        <div class="flex gap-3">
                            <a href="<?php echo e(route('admin.exams.show', $exam)); ?>" class="btn-secondary">Open</a>
                            <a href="<?php echo e(route('admin.exams.edit', $exam)); ?>" class="btn-secondary">Edit</a>
                            <form method="POST" action="<?php echo e(route('admin.exams.destroy', $exam)); ?>" onsubmit="return confirm('Delete this exam and all user attempt data for it?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-secondary">Delete</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/exams/index.blade.php ENDPATH**/ ?>