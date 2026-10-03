<?php $__env->startSection('title', 'Nouveau rôle'); ?>
<?php $__env->startSection('page-title', 'Créer un rôle'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('parametres.roles.index')); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Annuler
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('parametres.roles.store')); ?>" class="max-w-2xl space-y-4">
    <?php echo csrf_field(); ?>

    <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Nom du rôle</label>
            <input type="text" name="nom" value="<?php echo e(old('nom')); ?>" required
                   placeholder="Ex : Stagiaire, Comptable senior, Auditeur externe..."
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
            <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-700 mb-2">Couleur du badge</label>
            <div class="flex gap-2">
                <?php $__currentLoopData = ['green' => 'bg-emerald-500', 'blue' => 'bg-blue-500', 'amber' => 'bg-amber-500', 'red' => 'bg-red-500', 'purple' => 'bg-purple-500', 'gray' => 'bg-gray-400']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $bg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="cursor-pointer">
                    <input type="radio" name="couleur" value="<?php echo e($val); ?>" class="sr-only peer" <?php echo e(old('couleur') === $val ? 'checked' : ($val === 'blue' && !old('couleur') ? 'checked' : '')); ?>>
                    <span class="w-7 h-7 rounded-full <?php echo e($bg); ?> block ring-2 ring-offset-2 ring-transparent peer-checked:ring-gray-400 transition"></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <p class="text-xs font-medium text-gray-700">Pages accessibles</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Décochez une page pour la verrouiller complètement à ce rôle.</p>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 cursor-pointer transition">
                <input type="checkbox" name="pages[]" value="<?php echo e($key); ?>"
                       <?php echo e(in_array($key, old('pages', array_keys($pages))) ? 'checked' : ''); ?>

                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-200">
                <span class="text-sm text-gray-800"><?php echo e($label); ?></span>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="<?php echo e(route('parametres.roles.index')); ?>" class="btn-secondary text-sm">Annuler</a>
        <button type="submit" class="btn-primary text-sm flex items-center gap-1">
            <i class="ti ti-check"></i> Créer le rôle
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/parametres/roles/create.blade.php ENDPATH**/ ?>