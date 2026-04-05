<div class="glass-card-strong rounded-[28px] p-5">
    <div class="mb-4 rounded-2xl px-4 py-3 text-sm <?php echo e($apiConfigured ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'); ?>">
        <?php echo e($apiConfigured ? 'Gemini API is configured. New AI-generated questions can be synced now.' : 'Gemini API key missing. Add GEMINI_API_KEY in .env to generate live questions.'); ?>

    </div>
    <form wire:submit="generate" class="grid gap-3 md:grid-cols-3 md:items-end">
        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Section</label>
            <select wire:model="sectionId" class="w-full rounded-2xl border-slate-200 bg-white/80">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $exam->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($section->id); ?>"><?php echo e($section->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Difficulty</label>
            <select wire:model="difficulty" class="w-full rounded-2xl border-slate-200 bg-white/80">
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Question Count</label>
            <div class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-sm text-slate-600">
                Auto by section:
                <?php
                    $selectedSection = $exam->sections->firstWhere('id', $sectionId);
                    $selectedType = $selectedSection?->type;
                    $count = match ($selectedType) {
                        'verbal_reasoning' => 44,
                        'decision_making' => 35,
                        'quantitative_reasoning' => 36,
                        'situational_judgement' => 69,
                        default => null,
                    };
                ?>
                <span class="font-semibold text-slate-900"><?php echo e($count ?? '-'); ?></span>
            </div>
        </div>
        <button class="btn-primary" type="submit">Generate Questions</button>
    </form>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status): ?>
        <p class="mt-3 text-sm text-slate-600"><?php echo e($status); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/html/resources/views/livewire/admin-question-generator.blade.php ENDPATH**/ ?>