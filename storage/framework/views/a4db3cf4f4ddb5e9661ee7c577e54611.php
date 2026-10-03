<?php $__env->startSection('title','Compte de résultat'); ?>
<?php $__env->startSection('page-title','Compte de résultat — ' . $annee); ?>

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
<div class="max-w-2xl space-y-2">

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-emerald-50 flex items-center gap-2">
            <i class="ti ti-trending-up text-emerald-600"></i>
            <span class="text-sm font-semibold text-emerald-800">Produits d'exploitation (Classe 7)</span>
        </div>
        <?php $__currentLoopData = $produits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_120px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700"><?php echo e($compte->numero); ?> — <?php echo e($compte->libelle); ?></span>
            <span class="text-right font-mono font-medium text-emerald-600">
                <?php echo e(number_format($compte->solde, 0, ',', ' ')); ?>

            </span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_120px] px-4 py-3 bg-emerald-50 border-t border-emerald-100 text-xs font-semibold">
            <span class="text-emerald-800">Total produits</span>
            <span class="text-right font-mono text-emerald-700"><?php echo e(number_format($totalProduits, 0, ',', ' ')); ?></span>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-red-50 flex items-center gap-2">
            <i class="ti ti-trending-down text-red-500"></i>
            <span class="text-sm font-semibold text-red-700">Charges d'exploitation (Classe 6)</span>
        </div>
        <?php $__currentLoopData = $charges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_120px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700"><?php echo e($compte->numero); ?> — <?php echo e($compte->libelle); ?></span>
            <span class="text-right font-mono font-medium text-red-500">
                <?php echo e(number_format($compte->solde, 0, ',', ' ')); ?>

            </span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="grid grid-cols-[1fr_120px] px-4 py-3 bg-red-50 border-t border-red-100 text-xs font-semibold">
            <span class="text-red-700">Total charges</span>
            <span class="text-right font-mono text-red-600"><?php echo e(number_format($totalCharges, 0, ',', ' ')); ?></span>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-[1fr_120px] px-4 py-3.5 border-b border-gray-100 text-sm font-semibold">
            <span class="text-gray-800">Résultat brut avant impôt</span>
            <span class="text-right font-mono <?php echo e($resultatBrut >= 0 ? 'text-emerald-600':'text-red-500'); ?>">
                <?php echo e(($resultatBrut >= 0 ? '+' : '') . number_format($resultatBrut, 0, ',', ' ')); ?>

            </span>
        </div>
        <div class="grid grid-cols-[1fr_120px] px-4 py-2.5 text-xs text-gray-500 border-b border-gray-100">
            <span>Impôt sur les bénéfices (BIC 25%)</span>
            <span class="text-right font-mono text-red-400">−<?php echo e(number_format($bic, 0, ',', ' ')); ?></span>
        </div>
        <div class="grid grid-cols-[1fr_120px] px-4 py-4 bg-<?php echo e($resultatNet >= 0 ? 'emerald':'red'); ?>-50 text-sm font-bold">
            <span class="text-<?php echo e($resultatNet >= 0 ? 'emerald':'red'); ?>-800">Résultat net après impôt</span>
            <span class="text-right font-mono text-<?php echo e($resultatNet >= 0 ? 'emerald':'red'); ?>-700 text-lg">
                <?php echo e(($resultatNet >= 0 ? '+' : '') . number_format($resultatNet, 0, ',', ' ')); ?> F
            </span>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/bilan/resultat.blade.php ENDPATH**/ ?>