@extends('layouts.app')
@section('title', 'Facture ' . $facture->numero)
@section('page-title', 'Facture — ' . $facture->numero)

@section('topbar-actions')
    <a href="{{ route('factures.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
    @if(!in_array($facture->statut, ['payee','annulee']) && auth()->user()->peutEditer())
    <button onclick="openModal('modal-paiement')" class="btn-primary text-xs flex items-center gap-1">
        <i class="ti ti-cash"></i> Enregistrer paiement
    </button>
    <form method="POST" action="{{ route('factures.annuler', $facture) }}"
          onsubmit="return confirm('Annuler cette facture ?')">
        @csrf
        <button class="btn-danger text-xs flex items-center gap-1">
            <i class="ti ti-x"></i> Annuler
        </button>
    </form>
    @endif
@endsection

@section('content')
<div class="space-y-4 max-w-4xl">

    {{-- En-tête facture --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $facture->type === 'client' ? 'Facture client' : 'Facture fournisseur' }}</p>
                <h2 class="text-xl font-bold font-mono text-gray-900">{{ $facture->numero }}</h2>
            </div>
            <div class="text-right">
                @if($facture->statut === 'payee')        <span class="badge-green text-sm px-3 py-1">Payée</span>
                @elseif($facture->statut === 'partielle') <span class="badge-amber text-sm px-3 py-1">Partielle</span>
                @elseif($facture->statut === 'en_retard') <span class="badge-red text-sm px-3 py-1">En retard</span>
                @elseif($facture->statut === 'annulee')   <span class="badge-gray text-sm px-3 py-1">Annulée</span>
                @else <span class="badge-blue text-sm px-3 py-1">Émise</span>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 text-xs">
            <div>
                <p class="text-gray-400 mb-1">{{ $facture->type === 'client' ? 'Client' : 'Fournisseur' }}</p>
                <p class="font-semibold text-gray-900">{{ $facture->tiers->nom }}</p>
                <p class="text-gray-500">{{ $facture->tiers->ville }}</p>
            </div>
            <div>
                <p class="text-gray-400 mb-1">Date émission</p>
                <p class="font-medium text-gray-900">{{ $facture->date_emission->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-400 mb-1">Échéance</p>
                <p class="font-medium {{ $facture->isEnRetard() ? 'text-red-500' : 'text-gray-900' }}">
                    {{ $facture->date_echeance->format('d/m/Y') }}
                    @if($facture->isEnRetard())
                        <span class="text-red-400">({{ now()->diffInDays($facture->date_echeance) }}j de retard)</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Lignes facture --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-medium text-gray-800">Détail des prestations</span>
        </div>
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th">Désignation</th>
                    <th class="th text-right">Qté</th>
                    <th class="th">Unité</th>
                    <th class="th text-right">P.U. HT</th>
                    <th class="th text-right">TVA</th>
                    <th class="th text-right">Total HT</th>
                    <th class="th text-right">Total TTC</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($facture->lignes as $ligne)
                <tr>
                    <td class="td font-medium text-gray-900">{{ $ligne->designation }}</td>
                    <td class="td text-right font-mono">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                    <td class="td text-gray-500">{{ $ligne->unite ?? '—' }}</td>
                    <td class="td text-right font-mono">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }}</td>
                    <td class="td text-right text-gray-500">{{ $ligne->taux_tva }}%</td>
                    <td class="td text-right font-mono">{{ number_format($ligne->montant_ht, 0, ',', ' ') }}</td>
                    <td class="td text-right font-mono font-medium">{{ number_format($ligne->montant_ht + $ligne->montant_tva, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totaux --}}
        <div class="border-t border-gray-200 px-4 py-3 space-y-1.5 max-w-xs ml-auto">
            <div class="flex justify-between text-xs text-gray-600">
                <span>Sous-total HT</span>
                <span class="font-mono">{{ number_format($facture->montant_ht, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between text-xs text-gray-600">
                <span>TVA ({{ $facture->taux_tva }}%)</span>
                <span class="font-mono">{{ number_format($facture->montant_tva, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-gray-900 border-t border-gray-200 pt-1.5">
                <span>Total TTC</span>
                <span class="font-mono">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} F</span>
            </div>
            @if($facture->montant_paye > 0)
            <div class="flex justify-between text-xs text-emerald-600">
                <span>Déjà payé</span>
                <span class="font-mono">−{{ number_format($facture->montant_paye, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between text-sm font-bold {{ $facture->solde > 0 ? 'text-red-500' : 'text-emerald-600' }} border-t border-gray-200 pt-1.5">
                <span>Solde restant</span>
                <span class="font-mono">{{ number_format($facture->solde, 0, ',', ' ') }} F</span>
            </div>
            @endif
        </div>
    </div>

    @if($facture->notes)
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-400 mb-1">Notes</p>
        <p class="text-sm text-gray-700">{{ $facture->notes }}</p>
    </div>
    @endif

</div>

{{-- Modal paiement --}}
<x-modal id="modal-paiement" title="Enregistrer un paiement">
    <form method="POST" action="{{ route('factures.paiement', $facture) }}" class="space-y-4">
        @csrf
        <div class="bg-gray-50 rounded-lg p-3 text-xs space-y-1">
            <div class="flex justify-between text-gray-600">
                <span>Total TTC</span>
                <span class="font-mono font-medium">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Déjà payé</span>
                <span class="font-mono">{{ number_format($facture->montant_paye, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between font-semibold text-gray-800 border-t border-gray-200 pt-1">
                <span>Solde à payer</span>
                <span class="font-mono text-red-500">{{ number_format($facture->solde, 0, ',', ' ') }} F</span>
            </div>
        </div>
        <div>
            <label class="label">Montant du paiement (FCFA) <span class="text-red-500">*</span></label>
            <input type="number" name="montant" required min="0.01" step="1"
                   max="{{ $facture->solde }}"
                   value="{{ $facture->solde }}"
                   class="input" placeholder="1 200 000">
        </div>
        <div>
            <label class="label">Compte de règlement <span class="text-red-500">*</span></label>
            <select name="compte_reglement" class="input" required>
                <option value="512">512 — Banque (virement, chèque)</option>
                <option value="570">570 — Caisse (espèces)</option>
            </select>
            <p class="text-[11px] text-gray-400 mt-1">Une écriture comptable est générée automatiquement.</p>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeModal('modal-paiement')" class="btn-secondary text-xs">Annuler</button>
            <button type="submit" class="btn-primary text-xs"><i class="ti ti-check"></i> Valider le paiement</button>
        </div>
    </form>
</x-modal>

@push('before-modal')
{{-- Justificatifs de la facture --}}
<div class="mt-4">
    <x-zone-documents :parent="$facture" type="facture" />
</div>
@endpush
@endsection
