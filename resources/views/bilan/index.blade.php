@extends('layouts.app')

@section('title', 'Bilan')
@section('page-title', 'Bilan comptable — ' . $annee)

@section('topbar-actions')
    <form method="GET" class="flex items-end gap-2">
        <div>
            <label class="label">Exercice</label>
            <select name="annee" class="input text-xs" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" @selected($annee == $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>
    <a href="#" onclick="window.print()" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-printer"></i> Imprimer
    </a>
@endsection

@section('content')
<div class="grid grid-cols-2 gap-4">

    {{-- ACTIF --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <i class="ti ti-package text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800">ACTIF</span>
        </div>

        {{-- Actif immobilisé --}}
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Actif immobilisé (Cl. 2)</span>
        </div>
        @foreach($actif->filter(fn($c) => $c->classe === '2') as $compte)
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">{{ $compte->numero }} — {{ $compte->libelle }}</span>
            <span class="text-right font-mono font-medium">{{ number_format($compte->solde, 0, ',', ' ') }}</span>
        </div>
        @endforeach

        {{-- Actif circulant --}}
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Actif circulant (Cl. 3–4)</span>
        </div>
        @foreach($actif->filter(fn($c) => in_array($c->classe, ['3','4'])) as $compte)
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">{{ $compte->numero }} — {{ $compte->libelle }}</span>
            <span class="text-right font-mono font-medium">{{ number_format($compte->solde, 0, ',', ' ') }}</span>
        </div>
        @endforeach

        {{-- Trésorerie --}}
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Trésorerie (Cl. 5)</span>
        </div>
        @foreach($actif->filter(fn($c) => $c->classe === '5') as $compte)
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">{{ $compte->numero }} — {{ $compte->libelle }}</span>
            <span class="text-right font-mono font-medium">{{ number_format($compte->solde, 0, ',', ' ') }}</span>
        </div>
        @endforeach

        <div class="grid grid-cols-[1fr_90px] px-4 py-3 bg-gray-100 border-t border-gray-200 text-xs font-semibold">
            <span class="text-gray-800">TOTAL ACTIF</span>
            <span class="text-right font-mono text-gray-900">{{ number_format($totalActif, 0, ',', ' ') }}</span>
        </div>
    </div>

    {{-- PASSIF --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <i class="ti ti-scale text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800">PASSIF</span>
        </div>

        {{-- Capitaux propres --}}
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Capitaux propres (Cl. 1)</span>
        </div>
        @foreach($passif->filter(fn($c) => $c->classe === '1') as $compte)
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">{{ $compte->numero }} — {{ $compte->libelle }}</span>
            <span class="text-right font-mono font-medium">{{ number_format($compte->solde, 0, ',', ' ') }}</span>
        </div>
        @endforeach
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">Résultat de l'exercice {{ $annee }}</span>
            <span class="text-right font-mono font-medium {{ $resultatNet >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ ($resultatNet >= 0 ? '+' : '') . number_format($resultatNet, 0, ',', ' ') }}
            </span>
        </div>

        {{-- Dettes --}}
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
            <span class="text-[10px] uppercase tracking-wider font-medium text-gray-400">Dettes (Cl. 4)</span>
        </div>
        @foreach($passif->filter(fn($c) => $c->classe === '4') as $compte)
        <div class="grid grid-cols-[1fr_90px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">{{ $compte->numero }} — {{ $compte->libelle }}</span>
            <span class="text-right font-mono font-medium">{{ number_format($compte->solde, 0, ',', ' ') }}</span>
        </div>
        @endforeach

        <div class="grid grid-cols-[1fr_90px] px-4 py-3 bg-gray-100 border-t border-gray-200 text-xs font-semibold">
            <span class="text-gray-800">TOTAL PASSIF</span>
            <span class="text-right font-mono text-gray-900">{{ number_format($totalPassif + $resultatNet, 0, ',', ' ') }}</span>
        </div>
    </div>
</div>

@if(abs($totalActif - ($totalPassif + $resultatNet)) > 1)
<div class="mt-3 px-4 py-2.5 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700 flex items-center gap-2">
    <i class="ti ti-alert-triangle"></i>
    Le bilan n'est pas équilibré. Écart : {{ number_format(abs($totalActif - $totalPassif - $resultatNet), 0, ',', ' ') }} FCFA.
    Vérifiez vos écritures de clôture.
</div>
@else
<div class="mt-3 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-700 flex items-center gap-2">
    <i class="ti ti-circle-check"></i> Bilan équilibré — Actif = Passif + Résultat.
</div>
@endif
@endsection
