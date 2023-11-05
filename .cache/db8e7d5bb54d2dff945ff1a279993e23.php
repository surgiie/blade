name: <?php echo e($name); ?>

    favorite_food: <?php echo e($favoriteFood); ?>

<?php $__env->startComponent('/home/surgiie/projects/blade/component.yaml', ['data'=>'foobar']); ?>
<?php echo $__env->renderComponent(array (
  'spacing' => '        ',
)); ?> 
favorite_numbers:
<?php ($count = 0); ?>
<?php while($count < 3): ?>
    - '<?php echo e($count); ?>'
<?php ($count ++); ?>
<?php endwhile; ?><?php /**PATH /home/surgiie/projects/blade/test.txt ENDPATH**/ ?>