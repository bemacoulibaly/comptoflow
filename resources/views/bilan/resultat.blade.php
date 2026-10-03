@extends('layouts.app')
@section('title','Compte de résultat')
@section('page-title','Compte de résultat — ' . $annee)

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
<div class="max-w-2xl space-y-2">

    {{-- PRODUITS --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-emerald-50 flex items-center gap-2">
            <i class="ti ti-trending-up text-emerald-600"></i>
            <span class="text-sm font-semibold text-emerald-800">Produits d'exploitation (Classe 7)</span>
        </div>
        @foreach($produits as $compte)
        <div class="grid grid-cols-[1fr_120px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">{{ $compte->numero }} — {{ $compte->libelle }}</span>
            <span class="text-right font-mono font-medium text-emerald-600">
                {{ number_format($compte->solde, 0, ',', ' ') }}
            </span>
        </div>
        @endforeach
        <div class="grid grid-cols-[1fr_120px] px-4 py-3 bg-emerald-50 border-t border-emerald-100 text-xs font-semibold">
            <span class="text-emerald-800">Total produits</span>
            <span class="text-right font-mono text-emerald-700">{{ number_format($totalProduits, 0, ',', ' ') }}</span>
        </div>
    </div>

    {{-- CHARGES --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-red-50 flex items-center gap-2">
            <i class="ti ti-trending-down text-red-500"></i>
            <span class="text-sm font-semibold text-red-700">Charges d'exploitation (Classe 6)</span>
        </div>
        @foreach($charges as $compte)
        <div class="grid grid-cols-[1fr_120px] px-4 py-2 border-b border-gray-50 text-xs hover:bg-gray-50">
            <span class="text-gray-700">{{ $compte->numero }} — {{ $compte->libelle }}</span>
            <span class="text-right font-mono font-medium text-red-500">
                {{ number_format($compte->solde, 0, ',', ' ') }}
            </span>
        </div>
        @endforeach
        <div class="grid grid-cols-[1fr_120px] px-4 py-3 bg-red-50 border-t border-red-100 text-xs font-semibold">
            <span class="text-red-700">Total charges</span>
            <span class="text-right font-mono text-red-600">{{ number_format($totalCharges, 0, ',', ' ') }}</span>
        </div>
    </div>

    {{-- RÉSULTAT AVANT IMPÔT --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-[1fr_120px] px-4 py-3.5 border-b border-gray-100 text-sm font-semibold">
            <span class="text-gray-800">Résultat brut avant impôt</span>
            <span class="text-right font-mono {{ $resultatBrut >= 0 ? 'text-emerald-600':'text-red-500' }}">
                {{ ($resultatBrut >= 0 ? '+' : '') . number_format($resultatBrut, 0, ',', ' ') }}
            </span>
        </div>
        <div class="grid grid-cols-[1fr_120px] px-4 py-2.5 text-xs text-gray-500 border-b border-gray-100">
            <span>Impôt sur les bénéfices (BIC 25%)</span>
            <span class="text-right font-mono text-red-400">−{{ number_format($bic, 0, ',', ' ') }}</span>
        </div>
        <div class="grid grid-cols-[1fr_120px] px-4 py-4 bg-{{ $resultatNet >= 0 ? 'emerald':'red' }}-50 text-sm font-bold">
            <span class="text-{{ $resultatNet >= 0 ? 'emerald':'red' }}-800">Résultat net après impôt</span>
            <span class="text-right font-mono text-{{ $resultatNet >= 0 ? 'emerald':'red' }}-700 text-lg">
                {{ ($resultatNet >= 0 ? '+' : '') . number_format($resultatNet, 0, ',', ' ') }} F
            </span>
        </div>
    </div>

</div>
@endsection
