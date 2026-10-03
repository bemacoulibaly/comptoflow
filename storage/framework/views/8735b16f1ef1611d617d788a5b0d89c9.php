<?php $__env->startSection('title', 'Bilan'); ?>
<?php $__env->startSection('page-title', 'Bilan comptable — ' . $annee); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <form method="GET" class="flex items-end gap-2">
        <div>
            <label class="label">Exercice</label>
            <select name="annee" class="input text-xs" onchange="this.form.submit()">
                <?php for($y = now()->year; $y >= now()->year - 5; $y--): ?>
                    <option value="<?php echo e($y); ?>" <?php if($annee == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </form>
    <a href="#" onclick="window.print()" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-printer"></i> Imprimer
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-2 gap-4">

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <i class="ti ti-package text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800">ACTIF</span>
        </div>

        
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Actif immobilisé (Cl. 2)</span>
        </div>
        <?php $__currentLoopData = $actif->filter(fn($c) => $c->classe === '2'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700"><?php echo e($compte->numero); ?> — <?php echo e($compte->libelle); ?></span>
            <span class="text-right font-mono font-medium"><?php echo e(number_format($compte->solde, 0, ',', ' ')); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Actif circulant (Cl. 3–4)</span>
        </div>
        <?php $__currentLoopData = $actif->filter(fn($c) => in_array($c->classe, ['3','4'])); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700"><?php echo e($compte->numero); ?> — <?php echo e($compte->libelle); ?></span>
            <span class="text-right font-mono font-medium"><?php echo e(number_format($compte->solde, 0, ',', ' ')); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Trésorerie (Cl. 5)</span>
        </div>
        <?php $__currentLoopData = $actif->filter(fn($c) => $c->classe === '5'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700"><?php echo e($compte->numero); ?> — <?php echo e($compte->libelle); ?></span>
            <span class="text-right font-mono font-medium"><?php echo e(number_format($compte->solde, 0, ',', ' ')); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="grid grid-cols-[1fr_90px] px-4 py-3 bg-gray-100 border-t border-gray-200 text-xs font-semibold">
            <span class="text-gray-800">TOTAL ACTIF</span>
            <span class="text-right font-mono text-gray-900"><?php echo e(number_format($totalActif, 0, ',', ' ')); ?></span>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <i class="ti ti-scale text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800">PASSIF</span>
        </div>

        
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Capitaux propres (Cl. 1)</span>
        </div>
        <?php $__currentLoopData = $passif->filter(fn($c) => $c->classe === '1'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700"><?php echo e($compte->numero); ?> — <?php echo e($compte->libelle); ?></span>
            <span class="text-right font-mono font-medium"><?php echo e(number_format($compte->solde, 0, ',', ' ')); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">Résultat de l'exercice <?php echo e($annee); ?></span>
            <span class="text-right font-mono font-medium <?php echo e($resultatNet >= 0 ? 'text-emerald-600' : 'text-red-500'); ?>">
                <?php echo e(($resultatNet >= 0 ? '+' : '') . number_format($resultatNet, 0, ',', ' ')); ?>

            </span>
        </div>

        
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Dettes (Cl. 4)</span>
        </div>
        <?php $__currentLoopData = $passif->filter(fn($c) => $c->classe === '4'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700"><?php echo e($compte->numero); ?> — <?php echo e($compte->libelle); ?></span>
            <span class="text-right font-mono font-medium"><?php echo e(number_format($compte->solde, 0, ',', ' ')); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="grid grid-cols-[1fr_90px] px-4 py-3 bg-gray-100 border-t border-gray-200 text-xs font-semibold">
            <span class="text-gray-800">TOTAL PASSIF</span>
            <span class="text-right font-mono text-gray-900"><?php echo e(number_format($totalPassif + $resultatNet, 0, ',', ' ')); ?></span>
        </div>
    </div>
</div>

<?php if(abs($totalActif - ($totalPassif + $resultatNet)) > 1): ?>
<div class="mt-3 px-4 py-2.5 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700 flex items-center gap-2">
    <i class="ti ti-alert-triangle"></i>
    Le bilan n'est pas équilibré. Écart : <?php echo e(number_format(abs($totalActif - $totalPassif - $resultatNet), 0, ',', ' ')); ?> FCFA.
    Vérifiez vos écritures de clôture.
</div>
<?php else: ?>
<div class="mt-3 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-700 flex items-center gap-2">
    <i class="ti ti-circle-check"></i> Bilan équilibré — Actif = Passif + Résultat.
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/bilan/index.blade.php ENDPATH**/ ?>