@extends('layouts.app')
@section('title', $tiers->nom)
@section('page-title', $tiers->nom)

@section('topbar-actions')
    <a href="{{ route('tiers.edit', $tiers) }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-pencil"></i> Modifier
    </a>
    <a href="{{ route('tiers.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="grid grid-cols-3 gap-4">
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                    {{ $tiers->initiales }}
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900">{{ $tiers->nom }}</h2>
                    @if($tiers->type === 'client') <span class="badge-green">Client</span>
                    @elseif($tiers->type === 'fournisseur') <span class="badge-amber">Fournisseur</span>
                    @else <span class="badge-blue">Client & Fournisseur</span>
                    @endif
                </div>
            </div>
            <div class="space-y-2 text-xs">
                @if($tiers->email)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="ti ti-mail text-gray-400 w-4"></i> {{ $tiers->email }}
                </div>
                @endif
                @if($tiers->telephone)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="ti ti-phone text-gray-400 w-4"></i> {{ $tiers->telephone }}
                </div>
                @endif
                @if($tiers->adresse)
                <div class="flex items-start gap-2 text-gray-600">
                    <i class="ti ti-map-pin text-gray-400 w-4 mt-0.5"></i>
                    <span>{{ $tiers->adresse }}{{ $tiers->ville ? ', ' . $tiers->ville : '' }}</span>
                </div>
                @endif
                @if($tiers->numero_contribuable)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="ti ti-id text-gray-400 w-4"></i>
                    <span class="font-mono">{{ $tiers->numero_contribuable }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <span class="text-sm font-semibold text-gray-800">Historique des factures</span>
            </div>
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="th">N°</th>
                        <th class="th">Date</th>
                        <th class="th text-right">TTC</th>
                        <th class="th text-right">Solde</th>
                        <th class="th">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tiers->factures as $f)
                    <tr class="hover:bg-gray-50">
                        <td class="td font-mono text-gray-500">
                            <a href="{{ route('factures.show', $f) }}" class="hover:text-blue-600">{{ $f->numero }}</a>
                        </td>
                        <td class="td text-gray-500">{{ $f->date_emission->format('d/m/Y') }}</td>
                        <td class="td text-right font-mono">{{ number_format($f->montant_ttc, 0, ',', ' ') }}</td>
                        <td class="td text-right font-mono {{ $f->solde > 0 ? 'text-red-500' : 'text-emerald-600' }}">
                            {{ number_format($f->solde, 0, ',', ' ') }}
                        </td>
                        <td class="td">
                            @if($f->statut==='payee') <span class="badge-green">Payée</span>
                            @elseif($f->statut==='partielle') <span class="badge-amber">Partielle</span>
                            @elseif($f->statut==='en_retard') <span class="badge-red">Retard</span>
                            @else <span class="badge-blue">En cours</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="td text-center text-gray-400 py-8">Aucune facture.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
