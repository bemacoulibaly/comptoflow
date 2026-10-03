@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord — ' . now()->translatedFormat('F Y'))

@section('topbar-actions')
    {{-- Sélecteur de période --}}
    <form method="GET" class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
        @foreach(['mois'=>'Mois','trimestre'=>'Trimestre','annee'=>'Année'] as $val=>$label)
        <button type="submit" name="periode" value="{{ $val }}"
            class="px-3 py-1 rounded-md text-xs font-medium transition
                   {{ $periode === $val
                       ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                       : 'text-gray-500 dark:text-gray-400 hover:text-gray-900' }}">
            {{ $label }}
        </button>
        @endforeach
    </form>

    {{-- Mode sombre --}}
    <button id="btn-dark-mode"
            class="btn-secondary text-xs flex items-center gap-1"
            title="Mode sombre">
        <i class="ti ti-moon" id="icon-dark"></i>
        <i class="ti ti-sun hidden" id="icon-light"></i>
    </button>

    <a href="{{ route('ecritures.create') }}"
       class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Nouvelle écriture
    </a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- KPIs --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-cash"></i> Trésorerie
            </p>
            <p class="text-xl font-semibold font-mono dark:text-white">
                {{ number_format($stats['tresorerie'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-trending-up"></i> Recettes
            </p>
            <p class="text-xl font-semibold font-mono text-emerald-600">
                {{ number_format($stats['recettes'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">HT ce mois</p>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-trending-down"></i> Charges
            </p>
            <p class="text-xl font-semibold font-mono text-red-500">
                {{ number_format($stats['charges'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">HT ce mois</p>
        </div>
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1 flex items-center gap-1">
                <i class="ti ti-chart-line"></i> Résultat net
            </p>
            <p class="text-xl font-semibold font-mono {{ $stats['resultat'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ ($stats['resultat'] >= 0 ? '+' : '') . number_format($stats['resultat'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
        </div>
    </div>

    {{-- Graphique + Alertes --}}
    <div class="grid grid-cols-3 gap-4">

        {{-- Graphique Chart.js interactif --}}
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

        {{-- Alertes --}}
        <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-xl border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <i class="ti ti-bell text-gray-400"></i>
                <span class="text-sm font-medium text-gray-900 dark:text-white">Alertes</span>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <a href="{{ route('factures.index', ['statut'=>'en_retard']) }}"
                   class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">Factures en retard</span>
                    </div>
                    <span class="text-sm font-semibold text-red-500">{{ $stats['enRetard'] }}</span>
                </a>
                <a href="{{ route('ecritures.index', ['statut'=>'brouillon']) }}"
                   class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">Écritures à valider</span>
                    </div>
                    <span class="text-sm font-semibold text-amber-500">{{ $stats['aValider'] }}</span>
                </a>
                <a href="{{ route('tva.index') }}"
                   class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                        <span class="text-xs text-gray-700 dark:text-gray-300">TVA nette</span>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 font-mono">
                        {{ number_format($stats['tva'], 0, ',', ' ') }} F
                    </span>
                </a>
            </div>
            {{-- Prochaines échéances --}}
            <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700">
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-medium mb-2">
                    Prochaines échéances
                </p>
                @forelse($stats['echeances'] as $e)
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-xs text-gray-700 dark:text-gray-300 truncate flex-1">
                        {{ $e->titre }}
                    </span>
                    <span class="text-[11px] text-gray-400 ml-2 flex-shrink-0">
                        {{ $e->date_echeance->format('d/m') }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-gray-400">Aucune échéance à venir.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Données du serveur ────────────────────────────────────────
const evolutionData = @json($stats['evolutionMensuelle']);

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
@endpush
