@extends('layouts.app')
@section('title', 'Vérifier l\'import')
@section('page-title', 'Vérifier les opérations importées')

@section('topbar-actions')
    <a href="{{ route('import.create') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Recommencer
    </a>
@endsection

@section('content')
<div class="space-y-4">

    <div class="alert-info">
        <i class="ti ti-wand"></i>
        {{ $lignes->count() }} opération(s) détectée(s). Décochez celles à ignorer,
        corrigez les comptes si besoin, puis cliquez sur <strong>Importer</strong>.
        Toutes les écritures seront créées en <strong>brouillon</strong> — vous les validerez ensuite.
    </div>

    <form method="POST" action="{{ route('import.confirmer') }}">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <span class="text-sm font-semibold dark:text-white">Opérations détectées</span>
                <span class="text-xs text-gray-400">Compte relevé : {{ $compteReleve }}</span>
                <button type="button" id="btn-tout-cocher"
                        class="ml-auto text-xs text-blue-500 hover:text-blue-700">
                    Tout cocher
                </button>
                <button type="button" id="btn-tout-decocher"
                        class="text-xs text-gray-400 hover:text-gray-600">
                    Tout décocher
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-900/30 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="th w-8">✓</th>
                            <th class="th">Date</th>
                            <th class="th">Libellé</th>
                            <th class="th text-right">Montant</th>
                            <th class="th">Compte comptable</th>
                            <th class="th">Tiers détecté</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($lignes as $i => $l)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition ligne-row">
                            <td class="td">
                                <input type="checkbox"
                                       name="lignes[{{ $i }}][a_importer]"
                                       value="1" checked
                                       class="checkbox-import w-3.5 h-3.5 rounded text-emerald-600">
                            </td>
                            <td class="td">
                                <input type="date" name="lignes[{{ $i }}][date]"
                                       value="{{ $l['date'] }}"
                                       class="input text-xs py-1 px-2 w-32" required>
                            </td>
                            <td class="td">
                                <input type="text" name="lignes[{{ $i }}][libelle]"
                                       value="{{ $l['libelle'] }}"
                                       class="input text-xs py-1 px-2 w-full min-w-48" required>
                            </td>
                            <td class="td text-right">
                                <input type="number" name="lignes[{{ $i }}][montant]"
                                       value="{{ $l['montant'] }}" step="1"
                                       class="input text-xs py-1 px-2 w-32 text-right font-mono
                                              {{ $l['montant'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}"
                                       required>
                            </td>
                            <td class="td">
                                <select name="lignes[{{ $i }}][compte_suggere]"
                                        class="input text-xs py-1 px-2 w-48">
                                    @foreach($comptes as $num => $libelle)
                                    <option value="{{ $num }}"
                                        {{ $l['compte_suggere'] === $num ? 'selected' : '' }}>
                                        {{ $libelle }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="td">
                                @if($l['tiers_suggere'])
                                    <span class="badge-green">{{ $l['tiers_suggere']['nom'] }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-between items-center mt-4">
            <p class="text-xs text-gray-400">
                <span id="count-coches">{{ $lignes->count() }}</span> opération(s) sélectionnée(s)
            </p>
            <button type="submit" class="btn-primary text-sm flex items-center gap-2">
                <i class="ti ti-database-import"></i>
                Importer les écritures sélectionnées
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
const checkboxes = document.querySelectorAll('.checkbox-import');
const counter    = document.getElementById('count-coches');

function majCompteur() {
    counter.textContent = [...checkboxes].filter(c => c.checked).length;
}

checkboxes.forEach(cb => cb.addEventListener('change', majCompteur));

document.getElementById('btn-tout-cocher').addEventListener('click', () => {
    checkboxes.forEach(cb => cb.checked = true);
    majCompteur();
});

document.getElementById('btn-tout-decocher').addEventListener('click', () => {
    checkboxes.forEach(cb => cb.checked = false);
    majCompteur();
});
</script>
@endpush
