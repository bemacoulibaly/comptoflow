@extends('layouts.app')
@section('title','Rapprochement')
@section('page-title','Rapprochement — ' . $rapprochement->compte->libelle)
@section('topbar-actions')
    @if($rapprochement->statut==='en_cours' && auth()->user()->peutEditer())
    <button id="btn-suggestions"
            class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-wand"></i> Suggestions auto
    </button>
    <form method="POST" action="{{ route('rapprochement.valider',$rapprochement) }}">
        @csrf
        <button class="btn-primary text-xs flex items-center gap-1"><i class="ti ti-check"></i> Valider</button>
    </form>
    @endif
@endsection
@section('content')
<div class="space-y-4">
    {{-- KPIs --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Solde relevé</p>
            <p class="text-xl font-semibold font-mono">{{ number_format($rapprochement->solde_releve,0,',',' ') }} F</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-400 mb-1">Solde comptable</p>
            <p class="text-xl font-semibold font-mono">{{ number_format($rapprochement->solde_comptable,0,',',' ') }} F</p>
        </div>
        <div class="rounded-xl border p-4 {{ abs($rapprochement->ecart)<0.01 ? 'bg-emerald-50 border-emerald-200':'bg-amber-50 border-amber-200' }}">
            <p class="text-xs font-medium mb-1 {{ abs($rapprochement->ecart)<0.01?'text-emerald-600':'text-amber-600' }}">Écart</p>
            <p class="text-xl font-semibold font-mono {{ abs($rapprochement->ecart)<0.01?'text-emerald-700':'text-amber-700' }}" id="ecart-display">
                {{ number_format($rapprochement->ecart,0,',',' ') }} F
            </p>
        </div>
    </div>

    {{-- Panneau suggestions (caché par défaut) --}}
    <div id="suggestions-panel" class="hidden bg-white rounded-xl border border-blue-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-blue-100 flex items-center gap-2 bg-blue-50">
            <i class="ti ti-wand text-blue-500"></i>
            <span class="text-sm font-semibold text-blue-800">Suggestions automatiques</span>
            <span class="text-xs text-blue-500 ml-1">— Le système a trouvé ces correspondances probables</span>
            <button id="btn-fermer-suggestions" class="ml-auto text-blue-400 hover:text-blue-600">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div id="suggestions-list" class="divide-y divide-gray-100">
            <div class="px-4 py-6 text-center text-xs text-gray-400">
                <i class="ti ti-loader-2 animate-spin text-xl block mb-2"></i>
                Analyse en cours…
            </div>
        </div>
    </div>

    {{-- Table des opérations --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-list-check text-gray-400"></i>
            <span class="text-sm font-medium">Opérations</span>
            <span class="text-xs text-gray-400 ml-1">
                {{ $rapprochement->lignes->where('pointe',true)->count() }}/{{ $rapprochement->lignes->count() }} pointées
            </span>
        </div>
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="th w-8">✓</th>
                    <th class="th">Date</th>
                    <th class="th">Libellé</th>
                    <th class="th text-right">Montant</th>
                    <th class="th">Source</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($rapprochement->lignes as $ligne)
                <tr class="hover:bg-gray-50 transition {{ $ligne->pointe?'opacity-50 bg-emerald-50/30':'' }}"
                    id="ligne-{{ $ligne->id }}">
                    <td class="td">
                        <input type="checkbox"
                               class="pointer-checkbox w-3.5 h-3.5 rounded text-emerald-600 cursor-pointer"
                               data-id="{{ $ligne->id }}"
                               {{ $ligne->pointe?'checked':'' }}
                               {{ $rapprochement->statut==='valide'?'disabled':'' }}>
                    </td>
                    <td class="td text-gray-500">{{ $ligne->date_operation->format('d/m/Y') }}</td>
                    <td class="td font-medium">{{ $ligne->libelle }}</td>
                    <td class="td text-right font-mono {{ $ligne->montant<0?'text-red-500':'text-emerald-600' }}">
                        {{ number_format($ligne->montant,0,',',' ') }}
                    </td>
                    <td class="td">
                        @if($ligne->source==='releve')
                            <span class="badge-blue">Relevé</span>
                        @elseif($ligne->source==='comptabilite')
                            <span class="badge-green">Compta</span>
                        @else
                            <span class="badge-gray">Les deux</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const RAPP_ID = {{ $rapprochement->id }};

// ── Pointage manuel ──────────────────────────────────────────
document.querySelectorAll('.pointer-checkbox').forEach(cb => {
    cb.addEventListener('change', async function() {
        const res  = await fetch(`/rapprochement/ligne/${this.dataset.id}/pointer`, {
            method: 'PATCH',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
            body: JSON.stringify({pointe: this.checked})
        });
        const data = await res.json();
        if (data.ok) {
            const row = document.getElementById('ligne-' + this.dataset.id);
            row.classList.toggle('opacity-50', this.checked);
            row.classList.toggle('bg-emerald-50/30', this.checked);
            document.getElementById('ecart-display').textContent =
                new Intl.NumberFormat('fr-FR').format(data.ecart) + ' F';
        }
    });
});

// ── Suggestions automatiques ─────────────────────────────────
const panel = document.getElementById('suggestions-panel');
const list  = document.getElementById('suggestions-list');

document.getElementById('btn-suggestions').addEventListener('click', async () => {
    panel.classList.remove('hidden');
    list.innerHTML = `<div class="px-4 py-6 text-center text-xs text-gray-400">
        <i class="ti ti-loader-2 animate-spin text-xl block mb-2"></i>Analyse en cours…</div>`;

    const res  = await fetch(`/rapprochement/${RAPP_ID}/suggestions`);
    const data = await res.json();

    if (!data.length) {
        list.innerHTML = `<div class="px-4 py-6 text-center text-xs text-gray-400">
            <i class="ti ti-mood-empty text-2xl block mb-2"></i>
            Aucune correspondance automatique trouvée.<br>Pointez les lignes manuellement.</div>`;
        return;
    }

    const confBadge = c => c === 'haute'
        ? '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Haute confiance</span>'
        : c === 'moyenne'
            ? '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Confiance moyenne</span>'
            : '<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">Faible confiance</span>';

    list.innerHTML = data.map(s => `
        <div class="flex items-center gap-4 px-4 py-3 hover:bg-gray-50" id="sugg-${s.releve_id}-${s.compta_id}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    ${confBadge(s.confiance)}
                    <span class="text-[11px] text-gray-400">${s.ecart_jours === 0 ? 'Même jour' : s.ecart_jours + ' j. d\'écart'}</span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <p class="text-[10px] text-blue-500 font-medium uppercase mb-0.5">Relevé</p>
                        <p class="font-medium text-gray-900 truncate">${s.releve_libelle}</p>
                        <p class="text-gray-400">${s.releve_date} — <span class="font-mono">${new Intl.NumberFormat('fr-FR').format(s.releve_montant)} F</span></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-emerald-600 font-medium uppercase mb-0.5">Comptabilité</p>
                        <p class="font-medium text-gray-900 truncate">${s.compta_libelle}</p>
                        <p class="text-gray-400">${s.compta_date} — <span class="font-mono">${new Intl.NumberFormat('fr-FR').format(s.releve_montant)} F</span></p>
                    </div>
                </div>
            </div>
            <button onclick="appliquer(${s.releve_id}, ${s.compta_id})"
                    class="flex-shrink-0 btn-primary text-xs flex items-center gap-1">
                <i class="ti ti-check"></i> Valider
            </button>
        </div>
    `).join('');
});

document.getElementById('btn-fermer-suggestions').addEventListener('click', () => {
    panel.classList.add('hidden');
});

async function appliquer(releveId, comptaId) {
    const res  = await fetch(`/rapprochement/${RAPP_ID}/appliquer`, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF},
        body: JSON.stringify({releve_id: releveId, compta_id: comptaId})
    });
    const data = await res.json();
    if (data.ok) {
        // Cocher les deux lignes visuellement
        [releveId, comptaId].forEach(id => {
            const row = document.getElementById('ligne-' + id);
            const cb  = row?.querySelector('.pointer-checkbox');
            if (row) row.classList.add('opacity-50', 'bg-emerald-50/30');
            if (cb)  cb.checked = true;
        });
        // Retirer la suggestion de la liste
        const sugg = document.getElementById(`sugg-${releveId}-${comptaId}`);
        if (sugg) sugg.remove();
        // Mettre à jour l'écart affiché
        document.getElementById('ecart-display').textContent =
            new Intl.NumberFormat('fr-FR').format(data.ecart) + ' F';
    }
}
</script>
@endpush
