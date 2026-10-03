@extends('layouts.app')
@section('title', 'Grand livre')
@section('page-title', 'Grand livre — Compte ' . $compte->numero)

@section('topbar-actions')
    <form method="GET" class="flex items-end gap-2">
        <div>
            <label class="label">Compte</label>
            <input type="text" name="compte" value="{{ request('compte', $compte->numero) }}" class="input text-xs w-24" placeholder="512">
        </div>
        <div>
            <label class="label">Du</label>
            <input type="date" name="debut" value="{{ $debut }}" class="input text-xs">
        </div>
        <div>
            <label class="label">Au</label>
            <input type="date" name="fin" value="{{ $fin }}" class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs">Afficher</button>
    </form>
    <a href="{{ route('ecritures.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Résumé compte --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-6">
        <div>
            <p class="text-xs text-gray-400">Compte</p>
            <p class="font-mono font-semibold text-gray-900 text-lg">{{ $compte->numero }}</p>
        </div>
        <div class="flex-1">
            <p class="text-xs text-gray-400">Libellé</p>
            <p class="font-medium text-gray-900">{{ $compte->libelle }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Total débit</p>
            <p class="font-mono font-semibold text-gray-900">{{ number_format($total_debit, 0, ',', ' ') }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Total crédit</p>
            <p class="font-mono font-semibold text-gray-900">{{ number_format($total_credit, 0, ',', ' ') }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400">Solde final</p>
            <p class="font-mono font-bold text-lg {{ $solde_final >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                {{ ($solde_final >= 0 ? '+' : '') . number_format($solde_final, 0, ',', ' ') }}
            </p>
        </div>
    </div>

    {{-- Lignes --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">Date</th>
                    <th class="th">N° pièce</th>
                    <th class="th">Journal</th>
                    <th class="th">Libellé</th>
                    <th class="th text-right">Débit</th>
                    <th class="th text-right">Crédit</th>
                    <th class="th text-right">Solde cumulé</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lignes as $ligne)
                <tr class="hover:bg-gray-50">
                    <td class="td text-gray-500">{{ \Carbon\Carbon::parse($ligne['date'])->format('d/m/Y') }}</td>
                    <td class="td font-mono text-gray-500">{{ $ligne['piece'] }}</td>
                    <td class="td"><span class="badge-blue">{{ $ligne['libelle_journal'] ?? '—' }}</span></td>
                    <td class="td font-medium text-gray-900">{{ $ligne['libelle'] }}</td>
                    <td class="td text-right font-mono {{ $ligne['debit'] > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                        {{ $ligne['debit'] > 0 ? number_format($ligne['debit'], 0, ',', ' ') : '—' }}
                    </td>
                    <td class="td text-right font-mono {{ $ligne['credit'] > 0 ? 'text-gray-900' : 'text-gray-300' }}">
                        {{ $ligne['credit'] > 0 ? number_format($ligne['credit'], 0, ',', ' ') : '—' }}
                    </td>
                    <td class="td text-right font-mono font-medium {{ $ligne['solde'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ ($ligne['solde'] >= 0 ? '+' : '') . number_format($ligne['solde'], 0, ',', ' ') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="td text-center text-gray-400 py-10">
                        <i class="ti ti-inbox text-3xl block mb-2"></i>
                        Aucune écriture validée pour ce compte sur cette période.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
