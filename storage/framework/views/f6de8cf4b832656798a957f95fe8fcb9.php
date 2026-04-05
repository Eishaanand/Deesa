<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo e(route('admin.exams.index')); ?>" class="btn-secondary">Back</a>
        </div>

        <section class="glass-card rounded-[32px] p-8">
            <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr] xl:items-start">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Exam Detail</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950"><?php echo e($exam->title); ?></h1>
                    <p class="mt-2 text-sm text-slate-500">
                        Source mode: <?php echo e($exam->is_ai_supported ? 'Gemini' : 'Local'); ?>

                    </p>
                    <div class="mt-5 glass-card-strong rounded-[28px] p-5">
                        <div class="grid gap-3 md:grid-cols-3 md:items-end">
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Question Source</label>
                                <form method="POST" action="<?php echo e(route('admin.exams.toggle-source', $exam)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm font-semibold text-slate-700">
                                        <?php echo e($exam->is_ai_supported ? 'Gemini' : 'Local'); ?>

                                    </button>
                                </form>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Generate</label>
                                <form method="POST" action="<?php echo e(route('admin.exams.regenerate', $exam)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn-primary w-full">Generate</button>
                                </form>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Delete</label>
                                <form method="POST" action="<?php echo e(route('admin.exams.destroy', $exam)); ?>" onsubmit="return confirm('Delete this exam and all user attempt data for it?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm font-semibold text-slate-700">Delete Exam</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin-question-generator', ['exam' => $exam]);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-2634389264-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <section class="glass-card rounded-[24px] px-5 py-4 text-sm text-slate-700">
                <?php echo e(session('status')); ?>

            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section class="space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $exam->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $availableGeminiCount = \App\Models\Question::query()
                        ->where('source', 'gemini')
                        ->whereHas('section', fn ($query) => $query->where('type', $section->type)->where('exam_id', '!=', $exam->id))
                        ->count();

                    $availableLocalCount = \App\Models\Question::query()
                        ->whereIn('source', ['local_bank', 'manual', 'seed', 'fallback'])
                        ->whereHas('section', fn ($query) => $query->where('type', $section->type)->where('exam_id', '!=', $exam->id))
                        ->count();
                ?>
                <article class="glass-card rounded-[28px] p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="font-display text-2xl font-semibold text-slate-900"><?php echo e($section->name); ?></h2>
                            <p class="mt-2 text-sm text-slate-500"><?php echo e($section->questions->count()); ?> questions · <?php echo e(round($section->time_limit_seconds / 60)); ?> mins</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Available Gemini questions: <?php echo e($availableGeminiCount); ?>

                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Available local cached questions: <?php echo e($availableLocalCount); ?>

                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Questions already in this exam: <?php echo e($section->questions->count()); ?>

                            </p>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/exams/show.blade.php ENDPATH**/ ?>