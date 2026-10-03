<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['route', 'icon']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['route', 'icon']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $active = request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route));
?>

<a href="<?php echo e(route($route)); ?>"
   class="flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition
          <?php echo e($active
              ? 'bg-gray-100 text-gray-900 font-medium'
              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'); ?>">
    <i class="ti <?php echo e($icon); ?> text-sm flex-shrink-0"></i>
    <?php echo e($slot); ?>

</a>
<?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/components/nav-item.blade.php ENDPATH**/ ?>