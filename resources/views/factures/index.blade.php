@extends('layouts.app')

@section('title', 'Factures')
@section('page-title', 'Factures clients & fournisseurs')

@section('topbar-actions')
    <a href="{{ route('factures.create', ['type' => 'fournisseur']) }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Facture fournisseur
    </a>
    <a href="{{ route('factures.create', ['type' => 'client']) }}" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-plus"></i> Facture client
    </a>
@endsection

@section('content')
<div class="space-y-4">

    {{-- KPIs rapides --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">CA ce mois (HT)</p>
            <p class="text-lg font-semibold font-mono text-emerald-600">{{ number_format($stats['recettes_ht'],0,',',' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">Achats ce mois (HT)</p>
            <p class="text-lg font-semibold font-mono text-red-500">{{ number_format($stats['charges_ht'],0,',',' ') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">Factures en retard</p>
            <p class="text-lg font-semibold font-mono {{ $stats['factures_en_retard'] ? 'text-red-500' : 'text-gray-900' }}">
                {{ $stats['factures_en_retard'] }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3">
            <p class="text-xs text-gray-400">TVA collectée</p>
            <p class="text-lg font-semibold font-mono text-blue-600">{{ number_format($stats['tva_collectee'],0,',',' ') }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-200 p-3 flex flex-wrap items-end gap-3">
        <div>
            <label class="label">Type</label>
            <select name="type" class="input text-xs">
                <option value="">Toutes</option>
                <option value="client"      @selected(request('type')==='client')>Clients</option>
                <option value="fournisseur" @selected(request('type')==='fournisseur')>Fournisseurs</option>
            </select>
        </div>
        <div>
            <label class="label">Statut</label>
            <select name="statut" class="input text-xs">
                <option value="">Tous</option>
                @foreach(['brouillon'=>'Brouillon','emise'=>'Émise','partielle'=>'Partielle','payee'=>'Payée','en_retard'=>'En retard','annulee'=>'Annulée'] as $val=>$lbl)
                <option value="{{ $val }}" @selected(request('statut')===$val)>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="label">Rechercher (client/fournisseur)</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du tiers…" class="input text-xs">
        </div>
        <button type="submit" class="btn-primary text-xs">Filtrer</button>
        <a href="{{ route('factures.index') }}" class="btn-secondary text-xs">Réinitialiser</a>
    </form>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">N°</th>
                    <th class="th">Type</th>
                    <th class="th">Tiers</th>
                    <th class="th">Émission</th>
                    <th class="th">Échéance</th>
                    <th class="th text-right">HT</th>
                    <th class="th text-right">TVA</th>
                    <th class="th text-right">TTC</th>
                    <th class="th text-right">Solde</th>
                    <th class="th">Statut</th>
                    <th class="th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($factures as $facture)
                <tr class="hover:bg-gray-50 transition {{ $facture->isEnRetard() ? 'bg-red-50/30' : '' }}">
                    <td class="td font-mono text-gray-500">{{ $facture->numero }}</td>
                    <td class="td">
                        @if($facture->type === 'client')
                            <span class="badge-green">Client</span>
                        @else
                            <span class="badge-amber">Fourn.</span>
                        @endif
                    </td>
                    <td class="td font-medium text-gray-900">{{ $facture->tiers->nom }}</td>
                    <td class="td text-gray-500">{{ $facture->date_emission->format('d/m/Y') }}</td>
                    <td class="td {{ $facture->isEnRetard() ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                        {{ $facture->date_echeance->format('d/m/Y') }}
                    </td>
                    <td class="td text-right font-mono">{{ number_format($facture->montant_ht, 0, ',', ' ') }}</td>
                    <td class="td text-right font-mono text-gray-400">{{ number_format($facture->montant_tva, 0, ',', ' ') }}</td>
                    <td class="td text-right font-mono font-medium">{{ number_format($facture->montant_ttc, 0, ',', ' ') }}</td>
                    <td class="td text-right font-mono {{ $facture->solde > 0 ? 'text-red-500' : 'text-emerald-600' }}">
                        {{ number_format($facture->solde, 0, ',', ' ') }}
                    </td>
                    <td class="td">
                        @switch($facture->statut)
                            @case('payee')     <span class="badge-green">Payée</span>    @break
                            @case('partielle') <span class="badge-amber">Partielle</span>@break
                            @case('en_retard') <span class="badge-red">En retard</span>  @break
                            @case('annulee')   <span class="badge-gray">Annulée</span>   @break
                            @case('emise')     <span class="badge-blue">Émise</span>     @break
                            @default           <span class="badge-gray">Brouillon</span>
                        @endswitch
                    </td>
                    <td class="td">
                        <a href="{{ route('factures.show', $facture) }}" class="text-gray-400 hover:text-blue-600">
                            <i class="ti ti-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="td text-center text-gray-400 py-10">
                        <i class="ti ti-file-off text-3xl block mb-2"></i>
                        Aucune facture trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">{{ $factures->total() }} facture(s)</p>
            {{ $factures->links() }}
        </div>
    </div>
</div>
@endsection
