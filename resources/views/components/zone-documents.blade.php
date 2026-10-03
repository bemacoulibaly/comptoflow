{{--
  Zone de gestion des justificatifs — réutilisable sur toute page
  Usage : <x-zone-documents :parent="$ecriture" type="ecriture" />
         <x-zone-documents :parent="$facture"   type="facture" />
--}}

@props(['parent', 'type'])

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
     id="zone-documents-{{ $type }}-{{ $parent->id }}">

    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
        <i class="ti ti-paperclip text-gray-400"></i>
        <span class="text-sm font-semibold text-gray-800 dark:text-white flex-1">
            Justificatifs
        </span>
        <span class="text-xs text-gray-400" id="doc-count-{{ $type }}-{{ $parent->id }}">
            {{ $parent->documents->count() }} fichier(s)
        </span>
    </div>

    {{-- Liste des documents existants --}}
    <div class="divide-y divide-gray-100 dark:divide-gray-700"
         id="doc-list-{{ $type }}-{{ $parent->id }}">
        @forelse($parent->documents as $doc)
        <div class="flex items-center gap-3 px-4 py-2.5" id="doc-row-{{ $doc->id }}">
            <i class="ti {{ $doc->icone }} text-lg text-gray-400 flex-shrink-0"></i>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-900 dark:text-white truncate">
                    {{ $doc->nom_original }}
                </p>
                <p class="text-[11px] text-gray-400">
                    {{ ucfirst($doc->categorie) }} · {{ $doc->taille_formatee }}
                    · {{ $doc->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ $doc->url }}" target="_blank"
                   class="text-xs text-blue-500 hover:text-blue-700 flex items-center gap-1">
                    <i class="ti ti-download"></i>
                </a>
                @if(auth()->user()->peutEditer() || $doc->user_id === auth()->id())
                <button onclick="supprimerDoc({{ $doc->id }})"
                        class="text-xs text-red-400 hover:text-red-600">
                    <i class="ti ti-trash"></i>
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="px-4 py-6 text-center text-xs text-gray-400" id="doc-empty-{{ $type }}-{{ $parent->id }}">
            <i class="ti ti-files-off text-2xl block mb-1"></i>
            Aucun justificatif joint pour le moment.
        </div>
        @endforelse
    </div>

    {{-- Formulaire d'ajout --}}
    @if(auth()->user()->peutEditer())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
        <form class="doc-upload-form space-y-2"
              data-type="{{ $type }}"
              data-parent="{{ $parent->id }}">
            @csrf
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="flex items-center gap-2 px-3 py-2 border border-dashed border-gray-300
                                  dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-400
                                  transition text-xs text-gray-400">
                        <i class="ti ti-upload text-base"></i>
                        <span class="doc-filename">Cliquer pour joindre un fichier…</span>
                        <input type="file" class="hidden doc-file-input"
                               accept=".pdf,.jpg,.jpeg,.png,.webp,.xls,.xlsx,.csv">
                    </label>
                </div>
                <select class="input text-xs doc-categorie" style="width:140px">
                    <option value="facture">Facture</option>
                    <option value="devis">Devis</option>
                    <option value="contrat">Contrat</option>
                    <option value="recu">Reçu</option>
                    <option value="bon_livraison">Bon de livraison</option>
                    <option value="releve">Relevé</option>
                    <option value="autre" selected>Autre</option>
                </select>
            </div>
            <div class="flex gap-2 items-center">
                <input type="text" placeholder="Description (optionnel)"
                       class="input text-xs flex-1 doc-description">
                <button type="submit"
                        class="btn-primary text-xs flex items-center gap-1 flex-shrink-0">
                    <i class="ti ti-paperclip"></i> Joindre
                </button>
            </div>
            <p class="text-[11px] text-gray-400">PDF, image, Excel ou CSV · 10 Mo max</p>
        </form>
    </div>
    @endif
</div>

@once
@push('scripts')
<script>
// ── Upload de justificatif ────────────────────────────────────
document.querySelectorAll('.doc-upload-form').forEach(form => {
    const fileInput = form.querySelector('.doc-file-input');
    const filename  = form.querySelector('.doc-filename');

    fileInput.addEventListener('change', () => {
        filename.textContent = fileInput.files[0]?.name || 'Cliquer pour joindre…';
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        if (!fileInput.files[0]) return;

        const fd = new FormData();
        fd.append('fichier',     fileInput.files[0]);
        fd.append('parent_type', form.dataset.type);
        fd.append('parent_id',   form.dataset.parent);
        fd.append('categorie',   form.querySelector('.doc-categorie').value);
        fd.append('description', form.querySelector('.doc-description').value);
        fd.append('_token',      form.querySelector('[name="_token"]').value);

        const res  = await fetch('/documents', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.id) {
            const key    = form.dataset.type + '-' + form.dataset.parent;
            const list   = document.getElementById('doc-list-' + key);
            const empty  = document.getElementById('doc-empty-' + key);
            const count  = document.getElementById('doc-count-' + key);

            if (empty) empty.remove();

            const row = document.createElement('div');
            row.id = 'doc-row-' + data.id;
            row.className = 'flex items-center gap-3 px-4 py-2.5';
            row.innerHTML = `
                <i class="ti ${data.icone} text-lg text-gray-400 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 truncate">${data.nom}</p>
                    <p class="text-[11px] text-gray-400">${data.categorie} · ${data.taille}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="${data.url}" target="_blank" class="text-xs text-blue-500 hover:text-blue-700">
                        <i class="ti ti-download"></i>
                    </a>
                    <button onclick="supprimerDoc(${data.id})" class="text-xs text-red-400 hover:text-red-600">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>`;
            list.insertBefore(row, list.firstChild);

            const nb = list.querySelectorAll('[id^="doc-row-"]').length;
            if (count) count.textContent = nb + ' fichier(s)';

            fileInput.value = '';
            filename.textContent = 'Cliquer pour joindre…';
            form.querySelector('.doc-description').value = '';
        }
    });
});

// ── Suppression de justificatif ───────────────────────────────
async function supprimerDoc(id) {
    if (!confirm('Supprimer ce justificatif ?')) return;
    const res = await fetch('/documents/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    });
    const data = await res.json();
    if (data.ok) {
        const row = document.getElementById('doc-row-' + id);
        if (row) row.remove();
    }
}
</script>
@endpush
@endonce
