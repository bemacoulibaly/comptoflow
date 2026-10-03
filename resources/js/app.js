import './bootstrap';

// ── Icônes Tabler ───────────────────────────────────────────
import '@tabler/icons-webfont/dist/tabler-icons.css';

// ── Utilitaires globaux ─────────────────────────────────────

/**
 * Formatage monétaire FCFA
 */
window.formatFcfa = (n) =>
    Math.round(n).toLocaleString('fr-FR') + ' F';

/**
 * Formatage nombre simple
 */
window.formatNum = (n) =>
    Math.round(n).toLocaleString('fr-FR');

/**
 * Confirmation avant action destructive
 */
window.confirmer = (message = 'Êtes-vous sûr ?') =>
    window.confirm(message);

/**
 * Flash message disparaît automatiquement après 4s
 */
document.addEventListener('DOMContentLoaded', () => {

    // Auto-hide flash messages
    const flashes = document.querySelectorAll('[data-flash]');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    // Formulaires avec confirmation
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('submit', function(e) {
            const msg = this.dataset.confirm || 'Confirmer cette action ?';
            if (!confirm(msg)) e.preventDefault();
        });
    });

    // Auto-submit des selects de filtre
    document.querySelectorAll('select[data-autosubmit]').forEach(sel => {
        sel.addEventListener('change', function() {
            this.closest('form')?.submit();
        });
    });

    // Tooltip simple sur les éléments [title]
    document.querySelectorAll('[title]').forEach(el => {
        el.setAttribute('data-title', el.getAttribute('title'));
    });

    // Copie dans le presse-papier
    document.querySelectorAll('[data-copy]').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                const orig = this.innerHTML;
                this.innerHTML = '<i class="ti ti-check"></i>';
                setTimeout(() => { this.innerHTML = orig; }, 1500);
            });
        });
    });

});

// ── Plan comptable OHADA (autocomplétion) ───────────────────
export const planComptesOhada = {
    '101': 'Capital social',
    '401': 'Fournisseurs',
    '411': 'Clients',
    '421': 'Personnel — Rémunérations dues',
    '431': 'Sécurité sociale (CNPS)',
    '441': 'État — TVA collectée',
    '445': 'État — TVA déductible',
    '512': 'Banque',
    '570': 'Caisse',
    '601': 'Achats de marchandises',
    '611': 'Transports',
    '621': 'Locations et charges locatives',
    '628': 'Télécommunications',
    '641': 'Rémunérations du personnel',
    '681': 'Dotations aux amortissements',
    '695': 'Impôts sur les bénéfices (BIC)',
    '701': 'Ventes de marchandises',
    '706': 'Services vendus',
    '741': 'Subventions d\'exploitation',
};

export function getLibelleCompte(numero) {
    const key = Object.keys(planComptesOhada)
        .find(k => numero.startsWith(k));
    return key ? planComptesOhada[key] : (numero.length >= 3 ? 'Compte non reconnu' : '');
}

// ── Calcul TVA ───────────────────────────────────────────────
export function calculerTva(ht, taux = 18) {
    const montantTva = Math.round(ht * taux / 100 * 100) / 100;
    return {
        ht:   Math.round(ht * 100) / 100,
        tva:  montantTva,
        ttc:  Math.round((ht + montantTva) * 100) / 100,
    };
}

// ── Validation équilibre écriture ────────────────────────────
export function verifierEquilibre(lignes) {
    const totalDebit  = lignes.reduce((s, l) => s + (parseFloat(l.debit)  || 0), 0);
    const totalCredit = lignes.reduce((s, l) => s + (parseFloat(l.credit) || 0), 0);
    const diff        = Math.abs(totalDebit - totalCredit);
    return {
        totalDebit,
        totalCredit,
        diff,
        equilibree: diff < 0.01 && lignes.length >= 2,
    };
}

// ── Temps réel (Reverb/WebSocket) ───────────────────────────
import { initialiserTempsReel } from './echo';

document.addEventListener('DOMContentLoaded', () => {
    if (window.SOCIETE_ID) {
        initialiserTempsReel(window.SOCIETE_ID);
    }
});
