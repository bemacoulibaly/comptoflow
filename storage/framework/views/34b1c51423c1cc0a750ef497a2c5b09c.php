<?php $__env->startSection('title', 'Rôles'); ?>
<?php $__env->startSection('page-title', 'Rôles & permissions'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('parametres.roles.create')); ?>" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouveau rôle
    </a>
    <a href="<?php echo e(route('parametres.utilisateurs.index')); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Comptes
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-xs">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="th">Rôle</th>
                <th class="th">Type</th>
                <th class="th text-right">Comptes assignés</th>
                <th class="th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="hover:bg-gray-50 transition">
                <td class="td">
                    <span class="badge-<?php echo e($role->couleur); ?>"><?php echo e($role->nom); ?></span>
                </td>
                <td class="td text-gray-500">
                    <?php echo e($role->est_systeme ? 'Système (protégé)' : 'Personnalisé'); ?>

                </td>
                <td class="td text-right font-mono"><?php echo e($role->users_count); ?></td>
                <td class="td">
                    <div class="flex items-center justify-end gap-2">
                        <?php if(!$role->est_systeme): ?>
                        <a href="<?php echo e(route('parametres.roles.edit', $role)); ?>" class="text-gray-400 hover:text-blue-600">
                            <i class="ti ti-pencil"></i>
                        </a>
                        <form method="POST" action="<?php echo e(route('parametres.roles.destroy', $role)); ?>"
                              onsubmit="return confirm('Supprimer le rôle « <?php echo e($role->nom); ?> » ?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                        <?php else: ?>
                        <span class="text-gray-300" title="Rôle système, non modifiable">
                            <i class="ti ti-lock"></i>
                        </span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
    <i class="ti ti-info-circle text-amber-500 text-lg flex-shrink-0"></i>
    <p class="text-xs text-amber-800">
        L'administrateur a toujours accès à toutes les pages de l'application, quelle que soit la configuration des rôles.
        Les restrictions de page ne s'appliquent qu'aux comptes Éditeur, Lecteur, ou aux rôles personnalisés créés ici.
    </p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/parametres/roles/index.blade.php ENDPATH**/ ?>