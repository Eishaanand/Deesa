<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <a href="<?php echo e(route('admin.exams.index')); ?>" class="btn-secondary">Back</a>
    </div>
    <?php echo $__env->make('admin.exams.partials.form', [
        'title' => 'Create Exam',
        'action' => route('admin.exams.store'),
        'method' => 'POST',
        'exam' => null,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/exams/create.blade.php ENDPATH**/ ?>