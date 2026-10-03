<?php $__env->startSection('title', 'Comptes utilisateurs'); ?>
<?php $__env->startSection('page-title', 'Comptes utilisateurs'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('parametres.roles.index')); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-shield"></i> Gérer les rôles
    </a>
    <a href="<?php echo e(route('parametres.utilisateurs.create')); ?>" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-user-plus"></i> Créer un compte
    </a>
    <a href="<?php echo e(route('parametres.index')); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Paramètres
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-xs">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="th">Utilisateur</th>
                <th class="th">Email</th>
                <th class="th">Rôle</th>
                <th class="th">Statut</th>
                <th class="th text-right">Écritures</th>
                <th class="th text-right">Factures</th>
                <th class="th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="hover:bg-gray-50 transition <?php echo e(!$u->actif ? 'opacity-50' : ''); ?>">
                <td class="td">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-[10px] font-semibold flex-shrink-0">
                            <?php echo e($u->initiales); ?>

                        </div>
                        <span class="font-medium text-gray-900"><?php echo e($u->nom_complet); ?></span>
                        <?php if($u->id === auth()->id()): ?>
                        <span class="text-[10px] text-gray-400">(vous)</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="td text-gray-500"><?php echo e($u->email); ?></td>
                <td class="td">
                    <?php ($couleur = $u->roleAssigne->couleur ?? ($u->role === 'admin' ? 'green' : ($u->role === 'editeur' ? 'blue' : 'gray'))); ?>
                    <span class="badge-<?php echo e($couleur); ?>"><?php echo e($u->nom_role_affichage); ?></span>
                </td>
                <td class="td">
                    <?php if($u->actif): ?>
                        <span class="badge-green">Actif</span>
                    <?php else: ?>
                        <span class="badge-red">Désactivé</span>
                    <?php endif; ?>
                </td>
                <td class="td text-right font-mono"><?php echo e($u->ecritures_count); ?></td>
                <td class="td text-right font-mono"><?php echo e($u->factures_count); ?></td>
                <td class="td">
                    <a href="<?php echo e(route('parametres.utilisateurs.show', $u)); ?>" class="text-gray-400 hover:text-blue-600">
                        <i class="ti ti-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/parametres/utilisateurs/index.blade.php ENDPATH**/ ?>