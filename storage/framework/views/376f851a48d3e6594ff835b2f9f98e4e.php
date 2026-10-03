<?php $__env->startSection('title', 'Factures'); ?>
<?php $__env->startSection('page-title', 'Factures clients & fournisseurs'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('factures.create', ['type' => 'fournisseur'])); ?>" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Facture fournisseur
    </a>
    <a href="<?php echo e(route('factures.create', ['type' => 'client'])); ?>" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Facture client
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">

    
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">CA ce mois (HT)</p>
            <p class="text-lg font-semibold font-mono text-emerald-600"><?php echo e(number_format($stats['recettes_ht'],0,',',' ')); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">Achats ce mois (HT)</p>
            <p class="text-lg font-semibold font-mono text-red-500"><?php echo e(number_format($stats['charges_ht'],0,',',' ')); ?></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">Factures en retard</p>
            <p class="text-lg font-semibold font-mono <?php echo e($stats['factures_en_retard'] ? 'text-red-500' : 'text-gray-900'); ?>">
                <?php echo e($stats['factures_en_retard']); ?>

            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">TVA collectée</p>
            <p class="text-lg font-semibold font-mono text-blue-600"><?php echo e(number_format($stats['tva_collectee'],0,',',' ')); ?></p>
        </div>
    </div>

    
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-3 flex flex-wrap items-end gap-3">
        <div>
            <label class="label">Type</label>
            <select name="type" class="input text-xs">
                <option value="">Toutes</option>
                <option value="client"      <?php if(request('type')==='client'): echo 'selected'; endif; ?>>Clients</option>
                <option value="fournisseur" <?php if(request('type')==='fournisseur'): echo 'selected'; endif; ?>>Fournisseurs</option>
            </select>
        </div>
        <div>
            <label class="label">Statut</label>
            <select name="statut" class="input text-xs">
                <option value="">Tous</option>
                <?php $__currentLoopData = ['brouillon'=>'Brouillon','emise'=>'Émise','partielle'=>'Partielle','payee'=>'Payée','en_retard'=>'En retard','annulee'=>'Annulée']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php if(request('statut')===$val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="flex-1">
            <label class="label">Rechercher (client/fournisseur)</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nom du tiers…" class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs">Filtrer</button>
        <a href="<?php echo e(route('factures.index')); ?>" class="btn-secondary text-xs">Réinitialiser</a>
    </form>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">N°</th>
                    <th class="th">Type</th>
                    <th class="th">Tiers</th>
                    <th class="th">Émission</th>
                    <th class="th">Échéance</th>
                    <th class="th text-right">HT</th>
                    <th class="th text-right">TVA</th>
                    <th class="th text-right">TTC</th>
                    <th class="th text-right">Solde</th>
                    <th class="th">Statut</th>
                    <th class="th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition <?php echo e($facture->isEnRetard() ? 'bg-red-50/30' : ''); ?>">
                    <td class="td font-mono text-gray-500"><?php echo e($facture->numero); ?></td>
                    <td class="td">
                        <?php if($facture->type === 'client'): ?>
                            <span class="badge-green">Client</span>
                        <?php else: ?>
                            <span class="badge-amber">Fourn.</span>
                        <?php endif; ?>
                    </td>
                    <td class="td font-medium text-gray-900"><?php echo e($facture->tiers->nom); ?></td>
                    <td class="td text-gray-500"><?php echo e($facture->date_emission->format('d/m/Y')); ?></td>
                    <td class="td <?php echo e($facture->isEnRetard() ? 'text-red-500 font-medium' : 'text-gray-500'); ?>">
                        <?php echo e($facture->date_echeance->format('d/m/Y')); ?>

                    </td>
                    <td class="td text-right font-mono"><?php echo e(number_format($facture->montant_ht, 0, ',', ' ')); ?></td>
                    <td class="td text-right font-mono text-gray-400"><?php echo e(number_format($facture->montant_tva, 0, ',', ' ')); ?></td>
                    <td class="td text-right font-mono font-medium"><?php echo e(number_format($facture->montant_ttc, 0, ',', ' ')); ?></td>
                    <td class="td text-right font-mono <?php echo e($facture->solde > 0 ? 'text-red-500' : 'text-emerald-600'); ?>">
                        <?php echo e(number_format($facture->solde, 0, ',', ' ')); ?>

                    </td>
                    <td class="td">
                        <?php switch($facture->statut):
                            case ('payee'): ?>     <span class="badge-green">Payée</span>    <?php break; ?>
                            <?php case ('partielle'): ?> <span class="badge-amber">Partielle</span><?php break; ?>
                            <?php case ('en_retard'): ?> <span class="badge-red">En retard</span>  <?php break; ?>
                            <?php case ('annulee'): ?>   <span class="badge-gray">Annulée</span>   <?php break; ?>
                            <?php case ('emise'): ?>     <span class="badge-blue">Émise</span>     <?php break; ?>
                            <?php default: ?>           <span class="badge-gray">Brouillon</span>
                        <?php endswitch; ?>
                    </td>
                    <td class="td">
                        <a href="<?php echo e(route('factures.show', $facture)); ?>" class="text-gray-400 hover:text-blue-600">
                            <i class="ti ti-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="11" class="td text-center text-gray-400 py-10">
                        <i class="ti ti-file-off text-3xl block mb-2"></i>
                        Aucune facture trouvée.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400"><?php echo e($factures->total()); ?> facture(s)</p>
            <?php echo e($factures->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/factures/index.blade.php ENDPATH**/ ?>