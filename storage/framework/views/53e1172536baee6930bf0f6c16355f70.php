<?php $__env->startSection('title', 'Rapprochement'); ?>
<?php $__env->startSection('page-title', 'Rapprochement bancaire'); ?>
<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('rapprochement.create')); ?>" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouveau
    </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-xs">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="th">Compte</th><th class="th">Période</th>
                <th class="th text-right">Solde relevé</th><th class="th text-right">Solde compta</th>
                <th class="th text-right">Écart</th><th class="th">Statut</th><th class="th"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $rapprochements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50">
                <td class="td font-medium"><?php echo e($r->compte->numero); ?> — <?php echo e($r->compte->libelle); ?></td>
                <td class="td text-gray-500"><?php echo e($r->date_debut->format('d/m/Y')); ?> → <?php echo e($r->date_fin->format('d/m/Y')); ?></td>
                <td class="td text-right font-mono"><?php echo e(number_format($r->solde_releve,0,',',' ')); ?></td>
                <td class="td text-right font-mono"><?php echo e(number_format($r->solde_comptable,0,',',' ')); ?></td>
                <td class="td text-right font-mono <?php echo e(abs($r->ecart)<0.01?'text-emerald-600':'text-amber-600'); ?>"><?php echo e(number_format($r->ecart,0,',',' ')); ?></td>
                <td class="td"><?php if($r->statut==='valide'): ?><span class="badge-green">Validé</span><?php else: ?><span class="badge-amber">En cours</span><?php endif; ?></td>
                <td class="td"><a href="<?php echo e(route('rapprochement.show',$r)); ?>" class="text-gray-400 hover:text-blue-600"><i class="ti ti-eye"></i></a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="td text-center text-gray-400 py-10"><i class="ti ti-arrows-exchange text-3xl block mb-2"></i>Aucun rapprochement.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100"><?php echo e($rapprochements->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/rapprochement/index.blade.php ENDPATH**/ ?>