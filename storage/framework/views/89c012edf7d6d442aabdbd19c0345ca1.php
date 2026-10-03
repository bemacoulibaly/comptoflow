<?php $__env->startSection('title', 'Tableau de bord'); ?>
<?php $__env->startSection('page-title', 'Tableau de bord — ' . now()->translatedFormat('F Y')); ?>

<?php $__env->startSection('topbar-actions'); ?>
    
    <form method="GET" class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
        <?php $__currentLoopData = ['mois'=>'Mois','trimestre'=>'Trimestre','annee'=>'Année']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button type="submit" name="periode" value="<?php echo e($val); ?>"
            class="px-3 py-1 rounded-md text-xs font-medium transition
                   <?php echo e($periode === $val
                       ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                       : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'); ?>">
            <?php echo e($label); ?>

        </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </form>

    
    <button id="btn-dark-mode"
            class="btn-secondary text-xs flex items-center gap-1"
            title="Mode sombre">
        <i class="ti ti-moon" id="icon-dark"></i>
        <i class="ti ti-sun hidden" id="icon-light"></i>
    </button>

    <a href="<?php echo e(route('ecritures.create')); ?>"
       class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouvelle écriture
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5">

    
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-cash"></i> Trésorerie
            </p>
            <p class="text-xl font-semibold font-mono dark:text-white">
                <?php echo e(number_format($stats['tresorerie'], 0, ',', ' ')); ?>

            </p>
            <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-trending-up"></i> Recettes
            </p>
            <p class="text-xl font-semibold font-mono text-emerald-600">
                <?php echo e(number_format($stats['recettes'], 0, ',', ' ')); ?>

            </p>
            <p class="text-xs text-gray-400 mt-0.5">HT ce mois</p>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-trending-down"></i> Charges
            </p>
            <p class="text-xl font-semibold font-mono text-red-500">
                <?php echo e(number_format($stats['charges'], 0, ',', ' ')); ?>

            </p>
            <p class="text-xs text-gray-400 mt-0.5">HT ce mois</p>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-chart-line"></i> Résultat net
            </p>
            <p class="text-xl font-semibold font-mono <?php echo e($stats['resultat'] >= 0 ? 'text-emerald-600' : 'text-red-500'); ?>">
                <?php echo e(($stats['resultat'] >= 0 ? '+' : '') . number_format($stats['resultat'], 0, ',', ' ')); ?>

            </p>
            <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
        </div>
    </div>

    
    <div class="grid grid-cols-3 gap-4">

        
        <div class="col-span-2 bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <i class="ti ti-chart-area text-gray-400"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">
                    Évolution Recettes / Charges (6 mois)
                </span>
                <div class="ml-auto flex items-center gap-4">
                    <span class="text-xs text-emerald-600 flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span> Recettes
                    </span>
                    <span class="text-xs text-red-500 flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-sm bg-red-400 inline-block"></span> Charges
                    </span>
                </div>
            </div>
            <div class="p-4">
                <canvas id="chart-evolution" height="140"></canvas>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <i class="ti ti-bell text-gray-400"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Alertes</span>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <a href="<?php echo e(route('factures.index', ['statut'=>'en_retard'])); ?>"
                   class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">Factures en retard</span>
                    </div>
                    <span class="text-sm font-semibold text-red-500"><?php echo e($stats['enRetard']); ?></span>
                </a>
                <a href="<?php echo e(route('ecritures.index', ['statut'=>'brouillon'])); ?>"
                   class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">Écritures à valider</span>
                    </div>
                    <span class="text-sm font-semibold text-amber-500"><?php echo e($stats['aValider']); ?></span>
                </a>
                <a href="<?php echo e(route('tva.index')); ?>"
                   class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">TVA nette</span>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 font-mono">
                        <?php echo e(number_format($stats['tva'], 0, ',', ' ')); ?> F
                    </span>
                </a>
            </div>
            
            <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-medium mb-2">
                    Prochaines échéances
                </p>
                <?php $__empty_1 = true; $__currentLoopData = $stats['echeances']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-xs text-gray-700 dark:text-gray-300 truncate flex-1">
                        <?php echo e($e->titre); ?>

                    </span>
                    <span class="text-[11px] text-gray-400 ml-2 flex-shrink-0">
                        <?php echo e($e->date_echeance->format('d/m')); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-xs text-gray-400">Aucune échéance à venir.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Données du serveur ────────────────────────────────────────
const evolutionData = <?php echo json_encode($stats['evolutionMensuelle'], 15, 512) ?>;

// ── Graphique Chart.js ────────────────────────────────────────
const isDark  = document.documentElement.classList.contains('dark');
const gridClr = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
const lblClr  = isDark ? '#9ca3af' : '#6b7280';

const ctx = document.getElementById('chart-evolution').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels:   evolutionData.map(m => m.label),
        datasets: [
            {
                label: 'Recettes HT',
                data:  evolutionData.map(m => m.recettes),
                backgroundColor: 'rgba(16,185,129,0.75)',
                borderRadius: 4,
                borderSkipped: false,
            },
            {
                label: 'Charges HT',
                data:  evolutionData.map(m => m.charges),
                backgroundColor: 'rgba(239,68,68,0.65)',
                borderRadius: 4,
                borderSkipped: false,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ' : '
                        + new Intl.NumberFormat('fr-FR').format(ctx.parsed.y) + ' F HT',
                },
            },
        },
        scales: {
            x: { grid: { color: gridClr }, ticks: { color: lblClr, font: { size: 11 } } },
            y: {
                grid: { color: gridClr },
                ticks: {
                    color: lblClr,
                    font: { size: 11 },
                    callback: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v) + ' F',
                },
            },
        },
    },
});

// ── Mode sombre ───────────────────────────────────────────────
const html      = document.documentElement;
const btnDark   = document.getElementById('btn-dark-mode');
const iconDark  = document.getElementById('icon-dark');
const iconLight = document.getElementById('icon-light');

function applyDark(dark) {
    html.classList.toggle('dark', dark);
    iconDark.classList.toggle('hidden', dark);
    iconLight.classList.toggle('hidden', !dark);
    localStorage.setItem('darkMode', dark ? '1' : '0');

    // Mettre à jour les couleurs du graphique
    const g = dark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const l = dark ? '#9ca3af' : '#6b7280';
    chart.options.scales.x.grid.color      = g;
    chart.options.scales.y.grid.color      = g;
    chart.options.scales.x.ticks.color     = l;
    chart.options.scales.y.ticks.color     = l;
    chart.update();
}

// Appliquer la préférence mémorisée
const savedDark = localStorage.getItem('darkMode') === '1'
    || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
applyDark(savedDark);

btnDark.addEventListener('click', () => applyDark(!html.classList.contains('dark')));
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/dashboard/index.blade.php ENDPATH**/ ?>