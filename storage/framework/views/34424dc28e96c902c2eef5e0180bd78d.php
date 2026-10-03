<?php $__env->startSection('title', 'Nouvelle écriture'); ?>
<?php $__env->startSection('page-title', 'Saisie d\'écriture'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('ecritures.store')); ?>" id="form-ecriture">
<?php echo csrf_field(); ?>

<div class="space-y-4 max-w-5xl">

    
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <h2 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="ti ti-file-description text-gray-400"></i> En-tête de l'écriture
            <span id="status-badge" class="ml-auto text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">
                Non équilibrée
            </span>
        </h2>
        <div class="grid grid-cols-3 gap-4 mb-3">
            <div>
                <label class="label">N° pièce</label>
                <input type="text" name="numero_piece" value="<?php echo e($numero); ?>" required class="input">
            </div>
            <div>
                <label class="label">Date de l'écriture</label>
                <input type="date" name="date_ecriture" value="<?php echo e(today()->toDateString()); ?>" required class="input">
            </div>
            <div>
                <label class="label">Journal</label>
                <select name="journal" id="journal" class="input">
                    <?php $__currentLoopData = ['BQ'=>'BQ — Banque','CA'=>'CA — Caisse','AC'=>'AC — Achats','VT'=>'VT — Ventes','OD'=>'OD — Opérations diverses','SA'=>'SA — Salaires']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($code); ?>" <?php if($journal===$code): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Libellé général</label>
                <input type="text" name="libelle" required maxlength="255" placeholder="Description de l'opération…" class="input">
            </div>
            <div>
                <label class="label">Référence tiers</label>
                <input type="text" name="reference_tiers" maxlength="191" placeholder="Client, fournisseur, banque…" class="input">
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ti ti-table text-gray-400"></i>
            <span class="text-sm font-semibold text-gray-800">Lignes d'écriture</span>
            <span class="text-xs text-gray-400 ml-1" id="nb-lignes">0 ligne</span>
        </div>

        
        <div class="grid grid-cols-[100px_1fr_130px_130px_36px] gap-2 px-4 py-2 bg-gray-50 border-b border-gray-100 text-xs font-medium text-gray-400">
            <span>N° compte</span>
            <span>Libellé</span>
            <span class="text-right">Débit (FCFA)</span>
            <span class="text-right">Crédit (FCFA)</span>
            <span></span>
        </div>

        <div id="lignes-container"></div>

        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
            <button type="button" id="btn-add-ligne"
                class="text-xs text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                <i class="ti ti-plus"></i> Ajouter une ligne
            </button>
        </div>

        
        <div class="grid grid-cols-3 border-t border-gray-200">
            <div class="px-4 py-3 border-r border-gray-100">
                <p class="text-xs text-gray-400">Total débit</p>
                <p class="text-base font-semibold font-mono text-gray-900" id="total-debit">0</p>
            </div>
            <div class="px-4 py-3 border-r border-gray-100">
                <p class="text-xs text-gray-400">Total crédit</p>
                <p class="text-base font-semibold font-mono text-gray-900" id="total-credit">0</p>
            </div>
            <div class="px-4 py-3">
                <p class="text-xs text-gray-400">Différence</p>
                <p class="text-base font-semibold font-mono" id="total-diff">0</p>
            </div>
        </div>
    </div>

    
    <div class="flex justify-end gap-3">
        <a href="<?php echo e(route('ecritures.index')); ?>" class="btn-secondary text-sm">Annuler</a>
        <button type="submit" id="btn-submit"
            class="btn-primary text-sm flex items-center gap-1" disabled>
            <i class="ti ti-device-floppy"></i> Enregistrer l'écriture
        </button>
    </div>
</div>
</form>


<script>
const planComptes = <?php echo json_encode($comptes->pluck('libelle', 'numero'), 512) ?>;
let ligneCount = 0;

function fmt(n) {
    return Math.round(n).toLocaleString('fr-FR');
}

function getHint(num) {
    const key = Object.keys(planComptes).find(k => num.startsWith(k));
    return key ? planComptes[key] : (num.length >= 3 ? '<span class="text-red-400">Compte non reconnu</span>' : '');
}

function addLigne(num = '', lib = '', deb = '', cred = '') {
    const i = ligneCount++;
    const container = document.getElementById('lignes-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-[100px_1fr_36px_130px_130px_36px] gap-2 px-4 py-2.5 border-b border-gray-100 items-start ligne-row';
    div.innerHTML = `
        <div>
            <input type="text" name="lignes[${i}][numero_compte]" value="${num}"
                placeholder="ex: 411" maxlength="10" required
                class="input text-xs font-mono compte-input" data-index="${i}">
            <div class="text-[10px] mt-0.5 compte-hint" id="hint-${i}">${getHint(num)}</div>
        </div>
        <input type="text" name="lignes[${i}][libelle]" value="${lib}"
            placeholder="Libellé de la ligne…" required class="input text-xs libelle-ia">
        <button type="button"
                title="Suggérer un compte avec l'IA"
                class="btn-ia-compte mt-0.5 text-purple-400 hover:text-purple-600 transition"
                data-index="${i}">
            <i class="ti ti-sparkles text-base"></i>
        </button>
        <input type="number" name="lignes[${i}][debit]" value="${deb}"
            placeholder="0" min="0" step="1"
            class="input text-xs text-right font-mono montant-input">
        <input type="number" name="lignes[${i}][credit]" value="${cred}"
            placeholder="0" min="0" step="1"
            class="input text-xs text-right font-mono montant-input">
        <button type="button" class="text-gray-300 hover:text-red-400 mt-1.5 del-btn" title="Supprimer">
            <i class="ti ti-trash text-sm"></i>
        </button>`;

    div.querySelector('.del-btn').addEventListener('click', () => {
        div.remove();
        recalc();
    });
    div.querySelector('.compte-input').addEventListener('input', function () {
        document.getElementById('hint-' + this.dataset.index).innerHTML = getHint(this.value);
    });
    div.querySelectorAll('.montant-input').forEach(inp => inp.addEventListener('input', recalc));

    // Bouton IA — suggère un compte à partir du libellé
    div.querySelector('.btn-ia-compte').addEventListener('click', async function () {
        const idx     = this.dataset.index;
        const libelle = div.querySelector('.libelle-ia').value.trim();
        if (!libelle) { alert('Saisissez d\'abord un libellé pour obtenir une suggestion.'); return; }

        const icon = this.querySelector('i');
        icon.className = 'ti ti-loader-2 animate-spin text-base';
        this.disabled  = true;

        try {
            const res  = await fetch('/ia/suggerer-compte', {
                method: 'POST',
                headers: {
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                    'Accept'       : 'application/json',
                },
                body: JSON.stringify({ libelle }),
            });
            const data = await res.json();

            if (data.erreur) {
                alert('IA : ' + data.erreur);
            } else {
                // Remplir le champ compte
                const compteInput = div.querySelector('.compte-input');
                compteInput.value = data.numero;
                document.getElementById('hint-' + idx).innerHTML =
                    `<span class="text-purple-500">✦ ${data.libelle}</span>`;

                // Afficher l'explication en tooltip temporaire
                const tip = document.createElement('div');
                tip.className = 'fixed z-50 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 shadow-lg max-w-xs';
                tip.innerHTML = `<strong>${data.numero}</strong> — ${data.libelle}<br><span class="text-gray-300">${data.explication}</span>`;
                document.body.appendChild(tip);
                const rect = this.getBoundingClientRect();
                tip.style.top  = (rect.bottom + 8 + window.scrollY) + 'px';
                tip.style.left = (rect.left + window.scrollX) + 'px';
                setTimeout(() => tip.remove(), 4000);
            }
        } catch (e) {
            alert('Erreur de connexion au service IA.');
        } finally {
            icon.className = 'ti ti-sparkles text-base';
            this.disabled  = false;
        }
    });

    container.appendChild(div);
    recalc();
}

function recalc() {
    let deb = 0, cred = 0;
    document.querySelectorAll('.ligne-row').forEach(row => {
        deb  += parseFloat(row.querySelector('[name*="debit"]')?.value  || 0);
        cred += parseFloat(row.querySelector('[name*="credit"]')?.value || 0);
    });
    const diff = deb - cred;
    const bal  = Math.abs(diff) < 0.01 && document.querySelectorAll('.ligne-row').length >= 2;

    document.getElementById('total-debit').textContent  = fmt(deb);
    document.getElementById('total-credit').textContent = fmt(cred);

    const diffEl = document.getElementById('total-diff');
    diffEl.textContent = fmt(Math.abs(diff));
    diffEl.className   = 'text-base font-semibold font-mono ' + (bal ? 'text-emerald-600' : 'text-red-500');

    const badge = document.getElementById('status-badge');
    if (bal) {
        badge.textContent = '✓ Équilibrée';
        badge.className   = 'ml-auto text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-medium';
    } else {
        badge.textContent = 'Non équilibrée';
        badge.className   = 'ml-auto text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium';
    }

    document.getElementById('btn-submit').disabled = !bal;

    const n = document.querySelectorAll('.ligne-row').length;
    document.getElementById('nb-lignes').textContent = n + ' ligne' + (n > 1 ? 's' : '');
}

document.getElementById('btn-add-ligne').addEventListener('click', () => addLigne());

// Lignes par défaut
addLigne('411', '', '', '');
addLigne('512', '', '', '');
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Comptoflow\resources\views/ecritures/create.blade.php ENDPATH**/ ?>