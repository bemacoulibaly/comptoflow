@extends('layouts.app')
@section('title', 'Déclaration TVA')
@section('page-title', 'Déclaration TVA — ' . $declaration->periode_debut->format('d/m/Y') . ' au ' . $declaration->periode_fin->format('d/m/Y'))

@section('topbar-actions')
    @if($declaration->statut === 'brouillon' && auth()->user()->peutEditer())
    <form method="POST" action="{{ route('tva.valider', $declaration) }}">
        @csrf
        <button class="btn-primary text-xs flex items-center gap-1">
            <i class="ti ti-check"></i> Valider la déclaration
        </button>
    </form>
    @endif
    <a href="{{ route('tva.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="max-w-2xl space-y-4">

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-800">Déclaration TVA</span>
            @if($declaration->statut === 'validee') <span class="badge-green">Validée</span>
            @elseif($declaration->statut === 'deposee') <span class="badge-blue">Déposée</span>
            @else <span class="badge-amber">Brouillon</span>
            @endif
        </div>

        <div class="px-4 py-3 grid grid-cols-2 gap-4 text-xs border-b border-gray-100">
            <div>
                <p class="text-gray-400 mb-1">Période</p>
                <p class="font-medium text-gray-900">
                    {{ $declaration->periode_debut->format('d/m/Y') }} →
                    {{ $declaration->periode_fin->format('d/m/Y') }}
                </p>
            </div>
            <div>
                <p class="text-gray-400 mb-1">Date d'échéance</p>
                <p class="font-medium {{ $declaration->date_echeance?->isPast() && $declaration->statut !== 'deposee' ? 'text-red-500' : 'text-gray-900' }}">
                    {{ $declaration->date_echeance?->format('d/m/Y') ?? '—' }}
                </p>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            <div class="flex items-center justify-between px-4 py-3">
                <div>
                    <p class="text-xs font-medium text-gray-800">TVA collectée</p>
                    <p class="text-[11px] text-gray-400">Sur ventes et prestations facturées</p>
                </div>
                <span class="font-mono font-semibold text-red-500">
                    {{ number_format($declaration->tva_collectee, 0, ',', ' ') }} F
                </span>
            </div>
            <div class="flex items-center justify-between px-4 py-3">
                <div>
                    <p class="text-xs font-medium text-gray-800">TVA déductible</p>
                    <p class="text-[11px] text-gray-400">Sur achats et charges fournisseurs</p>
                </div>
                <span class="font-mono font-semibold text-emerald-600">
                    −{{ number_format($declaration->tva_deductible, 0, ',', ' ') }} F
                </span>
            </div>
        </div>

        <div class="flex items-center justify-between px-4 py-4 bg-amber-50 border-t border-amber-100">
            <div>
                <p class="text-sm font-bold text-amber-800">TVA nette à reverser</p>
                <p class="text-xs text-amber-600">Taux standard CI : 18%</p>
            </div>
            <span class="font-mono font-bold text-2xl text-amber-700">
                {{ number_format($declaration->tva_nette, 0, ',', ' ') }} F
            </span>
        </div>
    </div>

    @if($declaration->validee_at)
    <div class="alert-success">
        <i class="ti ti-circle-check"></i>
        Déclaration validée le {{ $declaration->validee_at->format('d/m/Y à H:i') }}
    </div>
    @endif

</div>
@endsection
