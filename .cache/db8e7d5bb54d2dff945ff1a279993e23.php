{
    "name": "<?php echo e($name); ?>",
    "favorite_food": "<?php echo e($favoriteFood); ?>",
<?php if (isset($component)) { $__componentOriginal0906e22fd1d0fd076025a68eb539233e = $component; } ?>
<?php $component = Surgiie\Blade\AnonymousComponent::resolve(['view' => '/home/surgiie/projects/blade/test','data' => ['name' => $name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('/home/surgiie/projects/blade/test'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Surgiie\Blade\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name)]); ?>
<?php echo $__env->renderComponent(modifiers: array (
  'spacing' => '    ',
)); ?> 
<?php endif; ?>
<?php if (isset($__componentOriginal0906e22fd1d0fd076025a68eb539233e)): ?>
<?php $component = $__componentOriginal0906e22fd1d0fd076025a68eb539233e; ?>
<?php unset($__componentOriginal0906e22fd1d0fd076025a68eb539233e); ?>
<?php endif; ?>
}<?php /**PATH /home/surgiie/projects/blade/test.txt ENDPATH**/ ?>