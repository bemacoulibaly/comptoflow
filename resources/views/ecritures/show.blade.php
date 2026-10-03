@extends('layouts.app')
@section('title', 'Écriture ' . $ecriture->numero_piece)
@section('page-title', 'Écriture — ' . $ecriture->numero_piece)

@section('topbar-actions')
    @if($ecriture->statut === 'brouillon' && auth()->user()->peutEditer())
    <form method="POST" action="{{ route('ecritures.valider', $ecriture) }}">
        @csrf
        <button class="btn-primary text-xs flex items-center gap-1">
            <i class="ti ti-check"></i> Valider
        </button>
    </form>
    @endif
    <a href="{{ route('ecritures.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="space-y-4 max-w-4xl">

    {{-- En-tête --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="grid grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-gray-400 mb-1">N° pièce</p>
                <p class="font-mono font-medium text-gray-900">{{ $ecriture->numero_piece }}</p>
            </div>
            <div>
                <p class="text-gray-400 mb-1">Date</p>
                <p class="font-medium text-gray-900">{{ $ecriture->date_ecriture->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-400 mb-1">Journal</p>
                <span class="badge-blue">{{ $ecriture->journal }}</span>
            </div>
            <div class="col-span-2">
                <p class="text-gray-400 mb-1">Libellé</p>
                <p class="font-medium text-gray-900">{{ $ecriture->libelle }}</p>
            </div>
            <div>
                <p class="text-gray-400 mb-1">Statut</p>
                @if($ecriture->statut === 'validee') <span class="badge-green">Validée</span>
                @elseif($ecriture->statut === 'annulee') <span class="badge-red">Annulée</span>
                @else <span class="badge-amber">Brouillon</span>
                @endif
            </div>
            <div>
                <p class="text-gray-400 mb-1">Saisi par</p>
                <p class="text-gray-700">{{ $ecriture->user->nom_complet }}</p>
            </div>
            @if($ecriture->validee_at)
            <div>
                <p class="text-gray-400 mb-1">Validée le</p>
                <p class="text-gray-700">{{ $ecriture->validee_at->format('d/m/Y à H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Lignes --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-table text-gray-400"></i>
            <span class="text-sm font-medium text-gray-800">Lignes d'écriture</span>
        </div>
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">N° compte</th>
                    <th class="th">Libellé compte</th>
                    <th class="th">Libellé ligne</th>
                    <th class="th text-right">Débit (FCFA)</th>
                    <th class="th text-right">Crédit (FCFA)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($ecriture->lignes as $ligne)
                <tr class="hover:bg-gray-50">
                    <td class="td font-mono text-gray-500">{{ $ligne->compte->numero }}</td>
                    <td class="td text-gray-600">{{ $ligne->compte->libelle }}</td>
                    <td class="td font-medium text-gray-900">{{ $ligne->libelle }}</td>
                    <td class="td text-right font-mono {{ $ligne->debit > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                        {{ $ligne->debit > 0 ? number_format($ligne->debit, 0, ',', ' ') : '—' }}
                    </td>
                    <td class="td text-right font-mono {{ $ligne->credit > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                        {{ $ligne->credit > 0 ? number_format($ligne->credit, 0, ',', ' ') : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="3" class="td font-semibold text-gray-700">TOTAUX</td>
                    <td class="td text-right font-mono font-semibold text-gray-900">
                        {{ number_format($ecriture->total_debit, 0, ',', ' ') }}
                    </td>
                    <td class="td text-right font-mono font-semibold text-gray-900">
                        {{ number_format($ecriture->total_credit, 0, ',', ' ') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            @if($ecriture->isEquilibree())
                <span class="flex items-center gap-1 text-xs text-emerald-600">
                    <i class="ti ti-circle-check"></i> Écriture équilibrée
                </span>
            @else
                <span class="flex items-center gap-1 text-xs text-red-500">
                    <i class="ti ti-alert-triangle"></i>
                    Déséquilibre : {{ number_format(abs($ecriture->total_debit - $ecriture->total_credit), 0, ',', ' ') }} FCFA
                </span>
            @endif
        </div>
    </div>

    {{-- Justificatifs --}}
    <x-zone-documents :parent="$ecriture" type="ecriture" />

</div>
@endsection
