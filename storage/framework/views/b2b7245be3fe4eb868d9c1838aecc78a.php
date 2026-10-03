<?php $__env->startSection('title', 'Écritures'); ?>
<?php $__env->startSection('page-title', 'Journal des écritures'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('ecritures.grand-livre')); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-book"></i> Grand livre
    </a>
    <a href="<?php echo e(route('import.create')); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-table-import"></i> Importer relevé
    </a>
    <a href="<?php echo e(route('ecritures.create')); ?>" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouvelle écriture
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">

    
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-3 flex flex-wrap items-end gap-3">
        <div>
            <label class="label">Journal</label>
            <select name="journal" class="input text-xs">
                <option value="">Tous</option>
                <?php $__currentLoopData = ['BQ'=>'Banque','CA'=>'Caisse','AC'=>'Achats','VT'=>'Ventes','OD'=>'Opérations diverses','SA'=>'Salaires']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($code); ?>" <?php if(request('journal')===$code): echo 'selected'; endif; ?>><?php echo e($code); ?> — <?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label">Statut</label>
            <select name="statut" class="input text-xs">
                <option value="">Tous</option>
                <option value="brouillon"  <?php if(request('statut')==='brouillon'): echo 'selected'; endif; ?>>Brouillon</option>
                <option value="validee"    <?php if(request('statut')==='validee'): echo 'selected'; endif; ?>>Validée</option>
                <option value="annulee"    <?php if(request('statut')==='annulee'): echo 'selected'; endif; ?>>Annulée</option>
            </select>
        </div>
        <div>
            <label class="label">Du</label>
            <input type="date" name="debut" value="<?php echo e(request('debut')); ?>" class="input text-xs">
        </div>
        <div>
            <label class="label">Au</label>
            <input type="date" name="fin" value="<?php echo e(request('fin')); ?>" class="input text-xs">
        </div>
        <div class="flex-1">
            <label class="label">Recherche</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Libellé, n° pièce…" class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs">Filtrer</button>
        <a href="<?php echo e(route('ecritures.index')); ?>" class="btn-secondary text-xs">Réinitialiser</a>
    </form>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">N° pièce</th>
                    <th class="th">Date</th>
                    <th class="th">Journal</th>
                    <th class="th">Libellé</th>
                    <th class="th text-right">Débit</th>
                    <th class="th text-right">Crédit</th>
                    <th class="th">Saisi par</th>
                    <th class="th">Statut</th>
                    <th class="th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $ecritures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ecriture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="td font-mono text-gray-500"><?php echo e($ecriture->numero_piece); ?></td>
                    <td class="td text-gray-600"><?php echo e($ecriture->date_ecriture->format('d/m/Y')); ?></td>
                    <td class="td">
                        <span class="badge-blue"><?php echo e($ecriture->journal); ?></span>
                    </td>
                    <td class="td font-medium text-gray-900 max-w-xs truncate"><?php echo e($ecriture->libelle); ?></td>
                    <td class="td text-right font-mono"><?php echo e(number_format($ecriture->total_debit, 0, ',', ' ')); ?></td>
                    <td class="td text-right font-mono"><?php echo e(number_format($ecriture->total_credit, 0, ',', ' ')); ?></td>
                    <td class="td text-gray-500"><?php echo e($ecriture->user->nom_complet); ?></td>
                    <td class="td">
                        <?php if($ecriture->statut === 'validee'): ?>
                            <span class="badge-green">Validée</span>
                        <?php elseif($ecriture->statut === 'annulee'): ?>
                            <span class="badge-red">Annulée</span>
                        <?php else: ?>
                            <span class="badge-amber">Brouillon</span>
                        <?php endif; ?>
                    </td>
                    <td class="td">
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('ecritures.show', $ecriture)); ?>" class="text-gray-400 hover:text-blue-600">
                                <i class="ti ti-eye"></i>
                            </a>
                            <?php if($ecriture->statut === 'brouillon' && auth()->user()->peutEditer()): ?>
                            <form method="POST" action="<?php echo e(route('ecritures.valider', $ecriture)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-gray-400 hover:text-emerald-600" title="Valider">
                                    <i class="ti ti-check"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="td text-center text-gray-400 py-10">
                        <i class="ti ti-inbox text-3xl block mb-2"></i>
                        Aucune écriture trouvée.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-gray-100">
            <?php echo e($ecritures->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/ecritures/index.blade.php ENDPATH**/ ?>