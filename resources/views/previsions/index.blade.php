@extends('layouts.app')
@section('title','Prévisions de trésorerie')
@section('page-title','Prévisions de trésorerie — 6 mois')

@section('content')
<div class="space-y-5">

    {{-- Solde de départ + tendance --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-400 mb-1">Trésorerie actuelle</p>
            <p class="text-xl font-semibold font-mono dark:text-white">
                {{ number_format($soldeCourant, 0, ',', ' ') }} F
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-400 mb-1">Solde prévu dans 6 mois</p>
            <p class="text-xl font-semibold font-mono {{ $projections[5]['solde_prevu'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ number_format($projections[5]['solde_prevu'], 0, ',', ' ') }} F
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-400 mb-1">Tendance</p>
            <p class="text-sm font-semibold flex items-center gap-2 mt-1">
                @if($tendance === 'hausse')
                    <span class="text-emerald-600"><i class="ti ti-trend-up"></i> En hausse</span>
                @elseif($tendance === 'baisse')
                    <span class="text-red-500"><i class="ti ti-trend-down"></i> En baisse</span>
                @else
                    <span class="text-gray-500"><i class="ti ti-minus"></i> Stable</span>
                @endif
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-400 mb-1">Mois en tension</p>
            @php $tensions = collect($projections)->where('solde_prevu', '<', 0)->count(); @endphp
            <p class="text-xl font-semibold font-mono {{ $tensions > 0 ? 'text-red-500' : 'text-emerald-600' }}">
                {{ $tensions }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $tensions > 0 ? 'mois en négatif' : 'Trésorerie positive' }}</p>
        </div>
    </div>

    {{-- Graphique --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
            <i class="ti ti-chart-line text-gray-400"></i>
            <span class="text-sm font-semibold dark:text-white">Évolution prévisionnelle du solde</span>
            <span class="text-xs text-gray-400 ml-2">
                <span class="inline-block w-3 h-3 rounded bg-blue-400 mr-1"></span>Solde prévu
                <span class="inline-block w-3 h-3 rounded bg-emerald-400 mr-1 ml-3"></span>Encaissements
                <span class="inline-block w-3 h-3 rounded bg-red-400 mr-1 ml-3"></span>Décaissements
            </span>
            <span class="ml-auto text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                <i class="ti ti-info-circle"></i> Estimation basée sur les factures en cours
            </span>
        </div>
        <div class="p-4">
            <canvas id="chart-previsions" height="120"></canvas>
        </div>
    </div>

    {{-- Table détaillée --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <span class="text-sm font-semibold dark:text-white">Détail mois par mois</span>
        </div>
        <table class="w-full text-xs">
            <thead class="bg-gray-50 dark:bg-gray-900/30 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="th">Mois</th>
                    <th class="th text-right text-emerald-600">Encaissements attendus</th>
                    <th class="th text-right text-red-500">Décaissements prévus</th>
                    <th class="th text-right">Flux net</th>
                    <th class="th text-right font-semibold">Solde prévu</th>
                    <th class="th">Fiabilité</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($projections as $p)
                <tr class="{{ $p['solde_prevu'] < 0 ? 'bg-red-50 dark:bg-red-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30' }} transition">
                    <td class="td font-medium dark:text-white">{{ $p['label'] }}</td>
                    <td class="td text-right font-mono text-emerald-600">
                        {{ number_format($p['encaissements'], 0, ',', ' ') }} F
                    </td>
                    <td class="td text-right font-mono text-red-500">
                        {{ number_format($p['decaissements'], 0, ',', ' ') }} F
                    </td>
                    <td class="td text-right font-mono {{ $p['flux_net'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ ($p['flux_net'] >= 0 ? '+' : '') . number_format($p['flux_net'], 0, ',', ' ') }} F
                    </td>
                    <td class="td text-right font-mono font-semibold {{ $p['solde_prevu'] >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600' }}">
                        {{ number_format($p['solde_prevu'], 0, ',', ' ') }} F
                    </td>
                    <td class="td">
                        @if($p['certain'])
                            <span class="badge-green">Factures connues</span>
                        @else
                            <span class="badge-gray">Estimation</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const data = @json($projections);
const isDark = document.documentElement.classList.contains('dark');
const grid   = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
const lbl    = isDark ? '#9ca3af' : '#6b7280';

new Chart(document.getElementById('chart-previsions').getContext('2d'), {
    type: 'bar',
    data: {
        labels: data.map(m => m.label),
        datasets: [
            {
                type: 'line',
                label: 'Solde prévu',
                data: data.map(m => m.solde_prevu),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: data.map(m => m.solde_prevu < 0 ? '#ef4444' : '#3b82f6'),
                pointRadius: 5,
                yAxisID: 'y',
                order: 0,
            },
            {
                type: 'bar',
                label: 'Encaissements',
                data: data.map(m => m.encaissements),
                backgroundColor: 'rgba(16,185,129,0.65)',
                borderRadius: 4,
                yAxisID: 'y',
                order: 1,
            },
            {
                type: 'bar',
                label: 'Décaissements',
                data: data.map(m => -m.decaissements),
                backgroundColor: 'rgba(239,68,68,0.55)',
                borderRadius: 4,
                yAxisID: 'y',
                order: 2,
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
                    label: ctx => {
                        const v = Math.abs(ctx.parsed.y);
                        return ctx.dataset.label + ' : ' + new Intl.NumberFormat('fr-FR').format(v) + ' F';
                    },
                },
            },
        },
        scales: {
            x: { grid: { color: grid }, ticks: { color: lbl, font: { size: 11 } } },
            y: {
                grid: { color: grid },
                ticks: {
                    color: lbl, font: { size: 11 },
                    callback: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v) + ' F',
                },
            },
        },
    },
});
</script>
@endpush
