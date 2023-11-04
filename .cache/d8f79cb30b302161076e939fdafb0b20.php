name: <?php echo e($name); ?>

relationships:

<?php if($yes): ?>
             foo: bar
<?php else: ?>
             bar: baz
<?php endif; ?>

pets:
<?php $__currentLoopData = $dogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             - <?php echo e($dog); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH test.txt ENDPATH**/ ?>