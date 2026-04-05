<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <section class="glass-card rounded-[32px] p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Admin</p>
                    <h1 class="mt-3 font-display text-4xl font-semibold text-slate-950">Deesa UCAT AI Control center</h1>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn-secondary">Users</a>
                    <a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn-secondary">Notifications</a>
                    <a href="<?php echo e(route('admin.exams.index')); ?>" class="btn-secondary">Exams</a>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-4">
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Total users</p><p class="mt-3 text-4xl font-semibold text-slate-950"><?php echo e($metrics['total_users']); ?></p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Total students</p><p class="mt-3 text-4xl font-semibold text-slate-950"><?php echo e($metrics['total_students']); ?></p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Premium users</p><p class="mt-3 text-4xl font-semibold text-slate-950"><?php echo e($metrics['premium_users']); ?></p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Active users</p><p class="mt-3 text-4xl font-semibold text-slate-950"><?php echo e($metrics['active_users']); ?></p></div>
        </section>

        <section class="grid gap-6 xl:grid-cols-4">
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">MRR</p><p class="mt-3 text-4xl font-semibold text-slate-950">GBP <?php echo e($metrics['monthly_recurring_revenue']); ?></p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">ARR</p><p class="mt-3 text-4xl font-semibold text-slate-950">GBP <?php echo e($metrics['projected_annual_revenue']); ?></p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Avg score</p><p class="mt-3 text-4xl font-semibold text-slate-950"><?php echo e($metrics['average_score']); ?></p></div>
            <div class="glass-card rounded-[28px] p-6"><p class="text-sm text-slate-500">Avg accuracy</p><p class="mt-3 text-4xl font-semibold text-slate-950"><?php echo e($metrics['average_accuracy']); ?>%</p></div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Business Alerts</h2>
                <div class="mt-6 space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900"><?php echo e($alert['title']); ?></p>
                            <p class="mt-1 text-sm text-slate-500"><?php echo e($alert['message']); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500">No alerts right now.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Sales Dashboard</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="text-sm text-slate-500">Conversion</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950"><?php echo e($sales['conversion_rate']); ?>%</p>
                    </div>
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="text-sm text-slate-500">Free users</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950"><?php echo e($sales['free_users']); ?></p>
                    </div>
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="text-sm text-slate-500">Paying users</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950"><?php echo e($sales['premium_users']); ?></p>
                    </div>
                </div>
                <div class="mt-6 space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sales['recent_premium_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900"><?php echo e($user['name']); ?></p>
                            <p class="mt-1 text-sm text-slate-500"><?php echo e($user['email']); ?> · active until <?php echo e($user['premium_until']); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1fr_1fr]">
            <div class="glass-card rounded-[28px] p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-2xl font-semibold text-slate-900">Live Monitoring</h2>
                    <a href="<?php echo e(route('admin.exams.index')); ?>" class="btn-secondary">Manage Exams</a>
                </div>
                <div class="mt-6 space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $live; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="glass-card-strong rounded-3xl p-4">
                            <p class="font-semibold text-slate-900"><?php echo e($item['student']); ?></p>
                            <p class="mt-1 text-sm text-slate-500"><?php echo e($item['exam']); ?> · <?php echo e($item['section']); ?></p>
                            <p class="mt-1 text-sm text-slate-500"><?php echo e($item['time_elapsed_minutes']); ?> minutes elapsed</p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500">No students are currently taking an exam.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="glass-card rounded-[28px] p-6">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Performance Distribution</h2>
                <div class="mt-6 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $distribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $range => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="mb-2 flex items-center justify-between text-sm text-slate-600">
                                <span><?php echo e($range); ?></span>
                                <span><?php echo e($count); ?></span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-200">
                                <div class="h-3 rounded-full bg-slate-950" style="width: <?php echo e(min($count * 12, 100)); ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <section class="glass-card rounded-[28px] p-6">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-display text-2xl font-semibold text-slate-900">Recent Notifications</h2>
                <a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn-secondary">Open Notification Center</a>
            </div>
            <div class="mt-6 space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="glass-card-strong rounded-3xl p-4">
                        <p class="font-semibold text-slate-900"><?php echo e($notification['title']); ?></p>
                        <p class="mt-1 text-sm text-slate-500"><?php echo e($notification['recipient']); ?> · <?php echo e($notification['audience']); ?> · <?php echo e($notification['sent_at']); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500">No notifications have been sent yet.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>