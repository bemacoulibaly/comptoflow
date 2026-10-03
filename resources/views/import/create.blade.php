@extends('layouts.app')
@section('title', 'Importer un relevé bancaire')
@section('page-title', 'Import relevé bancaire')

@section('topbar-actions')
    <a href="{{ route('ecritures.index') }}" class="btn-secondary text-xs flex items-center gap-1">
        <i class="ti ti-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="max-w-xl space-y-4">

    <div class="alert-info">
        <i class="ti ti-info-circle"></i>
        Importez votre relevé bancaire en CSV ou Excel. Le système reconnaît automatiquement
        les colonnes date, libellé et montant, puis suggère les comptes et tiers correspondants.
        Vous validez ensuite ligne par ligne avant la création des écritures.
    </div>

    <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data"
          class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-4">
        @csrf

        <div>
            <label class="label">Compte bancaire concerné <span class="text-red-500">*</span></label>
            <select name="compte_releve" class="input" required>
                <option value="">— Sélectionner le compte —</option>
                @foreach($comptes as $compte)
                <option value="{{ $compte->numero }}">
                    {{ $compte->numero }} — {{ $compte->libelle }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="label">Fichier relevé <span class="text-red-500">*</span></label>
            <label class="flex flex-col items-center justify-center gap-2 px-4 py-8
                          border-2 border-dashed border-gray-300 dark:border-gray-600
                          rounded-xl cursor-pointer hover:border-blue-400 transition"
                   id="drop-zone">
                <i class="ti ti-cloud-upload text-3xl text-gray-300"></i>
                <span class="text-sm text-gray-500 dark:text-gray-400" id="file-label">
                    Glissez votre fichier ici ou cliquez pour parcourir
                </span>
                <span class="text-xs text-gray-400">CSV, Excel · 5 Mo max</span>
                <input type="file" name="fichier" accept=".csv,.txt,.xls,.xlsx"
                       class="hidden" id="file-input" required>
            </label>
            @error('fichier')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary text-sm flex items-center gap-2">
                <i class="ti ti-table-import"></i> Analyser le fichier
            </button>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
            Format CSV attendu (exemple) :
        </p>
        <pre class="text-[11px] text-gray-500 bg-gray-50 dark:bg-gray-900/40 rounded-lg p-3 overflow-x-auto">Date;Libellé;Montant
15/06/2025;VRT SARL TANA IMPORT;1200000
16/06/2025;LOYER BUREAUX PLATEAU;-450000
20/06/2025;SALAIRES JUIN 2025;-2100000</pre>
        <p class="text-[11px] text-gray-400 mt-2">
            Les séparateurs acceptés sont : point-virgule, virgule, ou tabulation.
            Les montants négatifs correspondent à des débits (sorties).
        </p>
    </div>

</div>
@endsection

@push('scripts')
<script>
const input = document.getElementById('file-input');
const label = document.getElementById('file-label');
input.addEventListener('change', () => {
    label.textContent = input.files[0]?.name || 'Glissez votre fichier ici ou cliquez pour parcourir';
});
</script>
@endpush
