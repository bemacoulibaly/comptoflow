@extends('layouts.app')
@section('title', 'Nouvelle facture')
@section('page-title', 'Nouvelle facture ' . ($type === 'client' ? 'client' : 'fournisseur'))

@section('content')
<form method="POST" action="{{ route('factures.store') }}" id="form-facture">
@csrf
<div class="space-y-4 max-w-5xl">

    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Informations générales</h2>
        <input type="hidden" name="type" value="{{ $type }}">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="label">{{ $type === 'client' ? 'Client' : 'Fournisseur' }} <span class="text-red-500">*</span></label>
                <select name="tiers_id" class="input" required>
                    <option value="">Sélectionner…</option>
                    @foreach($tiers as $t)
                    <option value="{{ $t->id }}" @selected(old('tiers_id') == $t->id)>{{ $t->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">TVA (%)</label>
                <input type="number" name="taux_tva" value="{{ old('taux_tva', $societe->taux_tva) }}"
                       class="input" step="0.01" min="0" max="100" id="taux-tva-global">
            </div>
            <div>
                <label class="label">Date d'émission <span class="text-red-500">*</span></label>
                <input type="date" name="date_emission" value="{{ today()->toDateString() }}" required class="input">
            </div>
            <div>
                <label class="label">Date d'échéance <span class="text-red-500">*</span></label>
                <input type="date" name="date_echeance" value="{{ today()->addDays(30)->toDateString() }}" required class="input">
            </div>
        </div>
        <div>
            <label class="label">Notes</label>
            <textarea name="notes" class="input" rows="2" placeholder="Conditions de paiement, références…"></textarea>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-list text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800">Lignes de facturation</span>
        </div>
        <div style="display:grid;grid-template-columns:2fr 80px 70px 120px 80px 110px 30px;gap:6px;padding:7px 15px;background:#f9fafb;border-bottom:1px solid #e5e7eb">
            @foreach(['Désignation','Qté','Unité','Prix HT','TVA %','Total HT',''] as $h)
            <span class="text-[11px] font-medium text-gray-400">{{ $h }}</span>
            @endforeach
        </div>
        <div id="lignes-container"></div>
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
            <button type="button" id="btn-add" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                <i class="ti ti-plus"></i> Ajouter une ligne
            </button>
        </div>

        <div class="border-t border-gray-200 px-4 py-3 space-y-1.5 max-w-xs ml-auto">
            <div class="flex justify-between text-xs text-gray-600">
                <span>Total HT</span>
                <span class="font-mono" id="total-ht">0 F</span>
            </div>
            <div class="flex justify-between text-xs text-gray-600">
                <span>TVA</span>
                <span class="font-mono" id="total-tva">0 F</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-gray-900 border-t border-gray-200 pt-1.5">
                <span>Total TTC</span>
                <span class="font-mono" id="total-ttc">0 F</span>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('factures.index') }}" class="btn-secondary text-sm">Annuler</a>
        <button type="submit" class="btn-primary text-sm"><i class="ti ti-device-floppy"></i> Créer la facture</button>
    </div>
</div>
</form>

<script>
let idx = 0;
const fmt = n => Math.round(n).toLocaleString('fr-FR');

function addLigne() {
    const i = idx++;
    const div = document.createElement('div');
    div.style.cssText = 'display:grid;grid-template-columns:2fr 80px 70px 120px 80px 110px 30px;gap:6px;padding:8px 16px;border-bottom:1px solid #f3f4f6;align-items:center';
    const tvaDef = document.getElementById('taux-tva-global').value || 18;
    div.innerHTML = `
        <input type="text" name="lignes[${i}][designation]" placeholder="Prestation de conseil…" required class="input text-xs">
        <input type="number" name="lignes[${i}][quantite]" value="1" min="0.01" step="0.01" required class="input text-xs text-right font-mono qte">
        <input type="text" name="lignes[${i}][unite]" placeholder="h" class="input text-xs text-center">
        <input type="number" name="lignes[${i}][prix_unitaire]" placeholder="0" min="0" step="1" required class="input text-xs text-right font-mono pu">
        <input type="number" name="lignes[${i}][taux_tva]" value="${tvaDef}" min="0" max="100" step="0.01" class="input text-xs text-right tva-rate">
        <span class="text-xs font-mono text-right text-gray-700 total-ht-ligne">0 F</span>
        <button type="button" onclick="this.closest('div').remove();recalc()" class="text-gray-300 hover:text-red-400">
            <i class="ti ti-trash text-sm"></i>
        </button>`;
    div.querySelectorAll('input').forEach(inp => inp.addEventListener('input', recalc));
    document.getElementById('lignes-container').appendChild(div);
    recalc();
}

function recalc() {
    let ht = 0, tva = 0;
    document.querySelectorAll('#lignes-container > div').forEach(row => {
        const q = parseFloat(row.querySelector('.qte').value) || 0;
        const p = parseFloat(row.querySelector('.pu').value) || 0;
        const t = parseFloat(row.querySelector('.tva-rate').value) || 0;
        const ligneHt = q * p;
        ht  += ligneHt;
        tva += ligneHt * t / 100;
        row.querySelector('.total-ht-ligne').textContent = fmt(ligneHt) + ' F';
    });
    document.getElementById('total-ht').textContent  = fmt(ht) + ' F';
    document.getElementById('total-tva').textContent = fmt(tva) + ' F';
    document.getElementById('total-ttc').textContent = fmt(ht + tva) + ' F';
}

document.getElementById('btn-add').addEventListener('click', addLigne);
addLigne(); // Ligne par défaut
</script>
@endsection
