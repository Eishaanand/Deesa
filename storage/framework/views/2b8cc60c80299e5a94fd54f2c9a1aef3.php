<div class="glass-card mx-auto max-w-3xl rounded-[32px] p-8">
    <h1 class="font-display text-4xl font-semibold text-slate-950"><?php echo e($title); ?></h1>
    <form class="mt-8 space-y-5" method="POST" action="<?php echo e($action); ?>" autocomplete="off">
        <?php echo csrf_field(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($method !== 'POST'): ?>
            <?php echo method_field($method); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Title</label>
            <input class="w-full rounded-2xl border-slate-200 bg-white/80" name="title" value="<?php echo e(old('title', $exam?->title ?? '')); ?>" placeholder="Enter exam title" autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Description</label>
            <textarea class="w-full rounded-2xl border-slate-200 bg-white/80" rows="5" name="description" placeholder="Enter exam description" autocapitalize="sentences" autocorrect="off" spellcheck="false"><?php echo e(old('description', $exam?->description ?? '')); ?></textarea>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <select class="w-full rounded-2xl border-slate-200 bg-white/80" name="status">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['draft', 'published', 'archived']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php if(old('status', $exam?->status ?? 'draft') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-8">
                <input id="is_ai_supported" type="checkbox" name="is_ai_supported" value="1" <?php if(old('is_ai_supported', $exam?->is_ai_supported ?? true)): echo 'checked'; endif; ?>>
                <label for="is_ai_supported" class="text-sm text-slate-700">Use Gemini generation. Turn this off to build from local cached questions instead.</label>
            </div>
        </div>
        <button class="btn-primary" type="submit">Save Exam</button>
    </form>
</div>
<?php /**PATH /var/www/html/resources/views/admin/exams/partials/form.blade.php ENDPATH**/ ?>