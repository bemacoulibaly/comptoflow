@extends('layouts.app')

@section('title', 'TVA')
@section('page-title', 'Déclarations TVA')

@section('topbar-actions')
    <form method="POST" action="{{ route('tva.generer') }}" class="flex items-end gap-2">
        @csrf
        <div>
            <label class="label">Du</label>
            <input type="date" name="periode_debut" value="{{ $debutMois }}" class="input text-xs">
        </div>
        <div>
            <label class="label">Au</label>
            <input type="date" name="periode_fin" value="{{ $finMois }}" class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs flex items-center gap-1">
            <i class="ti ti-refresh"></i> Générer
        </button>
    </form>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Aperçu mois en cours --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-receipt-tax text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800">Aperçu — {{ now()->translatedFormat('F Y') }}</span>
        </div>
        <div class="grid grid-cols-3 divide-x divide-gray-100">
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 mb-1">TVA collectée (ventes)</p>
                <p class="text-xl font-semibold font-mono text-red-500">
                    {{ number_format($apercu['tva_collectee'], 0, ',', ' ') }} F
                </p>
            </div>
            <div class="px-5 py-4">
                <p class="text-xs text-gray-400 mb-1">TVA déductible (achats)</p>
                <p class="text-xl font-semibold font-mono text-emerald-600">
                    {{ number_format($apercu['tva_deductible'], 0, ',', ' ') }} F
                </p>
            </div>
            <div class="px-5 py-4 bg-amber-50">
                <p class="text-xs text-amber-600 mb-1 font-medium">TVA nette à reverser</p>
                <p class="text-2xl font-bold font-mono text-amber-700">
                    {{ number_format($apercu['tva_nette'], 0, ',', ' ') }} F
                </p>
                <p class="text-xs text-amber-500 mt-1">Taux : 18% — Norme OHADA CI</p>
            </div>
        </div>
    </div>

    {{-- Historique --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-800">Historique des déclarations</span>
        </div>
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">Période</th>
                    <th class="th text-right">Collectée</th>
                    <th class="th text-right">Déductible</th>
                    <th class="th text-right">Nette</th>
                    <th class="th">Échéance</th>
                    <th class="th">Statut</th>
                    <th class="th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($historique as $decl)
                <tr class="hover:bg-gray-50">
                    <td class="td font-medium">
                        {{ $decl->periode_debut->format('d/m/Y') }} – {{ $decl->periode_fin->format('d/m/Y') }}
                    </td>
                    <td class="td text-right font-mono text-red-500">{{ number_format($decl->tva_collectee, 0, ',', ' ') }}</td>
                    <td class="td text-right font-mono text-emerald-600">{{ number_format($decl->tva_deductible, 0, ',', ' ') }}</td>
                    <td class="td text-right font-mono font-semibold">{{ number_format($decl->tva_nette, 0, ',', ' ') }}</td>
                    <td class="td text-gray-500">{{ $decl->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                    <td class="td">
                        @if($decl->statut === 'validee') <span class="badge-green">Validée</span>
                        @elseif($decl->statut === 'deposee') <span class="badge-blue">Déposée</span>
                        @else <span class="badge-amber">Brouillon</span>
                        @endif
                    </td>
                    <td class="td flex items-center gap-2">
                        <a href="{{ route('tva.show', $decl) }}" class="text-gray-400 hover:text-blue-600">
                            <i class="ti ti-eye"></i>
                        </a>
                        @if($decl->statut === 'brouillon' && auth()->user()->peutEditer())
                        <form method="POST" action="{{ route('tva.valider', $decl) }}">
                            @csrf
                            <button class="text-gray-400 hover:text-emerald-600" title="Valider">
                                <i class="ti ti-check"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="td text-center text-gray-400 py-8">
                        Aucune déclaration. Cliquez sur "Générer" pour créer la première.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
