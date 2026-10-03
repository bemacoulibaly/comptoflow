<?php

namespace App\Services;

use App\Models\Compte;
use App\Models\Ecriture;
use App\Models\Societe;
use App\Models\Tiers;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ImportBancaireService
{
    /**
     * Parse un fichier CSV ou Excel de relevé bancaire et retourne
     * les opérations détectées avec leur suggestion de tiers/compte.
     */
    public function parser(UploadedFile $fichier, Societe $societe): Collection
    {
        $lignes = $this->lireCSV($fichier);

        return $lignes->map(function (array $ligne) use ($societe) {
            $libelle = $ligne['libelle'] ?? '';
            $montant = $this->normaliserMontant($ligne['montant'] ?? '0');

            return [
                'date'            => $this->normaliserDate($ligne['date'] ?? ''),
                'libelle'         => $libelle,
                'montant'         => $montant,
                'sens'            => $montant >= 0 ? 'credit' : 'debit',
                'tiers_suggere'   => $this->suggererTiers($libelle, $societe),
                'compte_suggere'  => $this->suggererCompte($libelle, $montant, $societe),
                'a_importer'      => true,
            ];
        })->filter(fn($l) => $l['date'] && $l['montant'] != 0)->values();
    }

    /**
     * Crée les écritures comptables à partir des lignes confirmées.
     */
    public function importer(array $lignes, string $compteReleve, Societe $societe, EcritureService $ecritureService): int
    {
        $nb = 0;

        foreach ($lignes as $l) {
            if (!($l['a_importer'] ?? false)) continue;

            $montant = (float) $l['montant'];
            $date    = $l['date'];
            $libelle = $l['libelle'];
            $numero  = $ecritureService->genererNumeroPiece($societe, 'BQ');

            $ecriture = Ecriture::create([
                'societe_id'    => $societe->id,
                'user_id'       => auth()->id(),
                'numero_piece'  => $numero,
                'date_ecriture' => $date,
                'journal'       => 'BQ',
                'libelle'       => $libelle,
                'statut'        => 'brouillon',
            ]);

            $compteContrepartie = $l['compte_suggere'] ?? '471'; // Charges à classer

            if ($montant > 0) {
                // Crédit relevé = encaissement
                $this->ajouterLignes($ecriture, $societe, $compteReleve, $compteContrepartie, $montant, $libelle);
            } else {
                // Débit relevé = décaissement
                $this->ajouterLignes($ecriture, $societe, $compteContrepartie, $compteReleve, abs($montant), $libelle);
            }

            $nb++;
        }

        return $nb;
    }

    // ── Lecture CSV ──────────────────────────────────────────────

    private function lireCSV(UploadedFile $fichier): Collection
    {
        $contenu = file($fichier->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($contenu)) return collect();

        // Détecter le séparateur (virgule, point-virgule ou tabulation)
        $sep = $this->detecterSeparateur($contenu[0]);

        // Première ligne = en-têtes
        $entetes = str_getcsv(array_shift($contenu), $sep);
        $entetes = array_map('trim', array_map('mb_strtolower', $entetes));

        // Normaliser les noms de colonnes courants
        $map = [
            'date'     => ['date', 'date opération', 'date operation', 'dateop', 'date_operation'],
            'libelle'  => ['libellé', 'libelle', 'description', 'motif', 'label', 'details'],
            'montant'  => ['montant', 'amount', 'valeur', 'credit', 'débit', 'debit'],
        ];

        $colonnes = [];
        foreach ($map as $cle => $variantes) {
            foreach ($entetes as $i => $e) {
                if (in_array($e, $variantes)) {
                    $colonnes[$cle] = $i;
                    break;
                }
            }
        }

        return collect($contenu)->map(function ($ligne) use ($sep, $colonnes, $entetes) {
            $cells = str_getcsv($ligne, $sep);
            if (count($cells) < 2) return null;

            return [
                'date'    => $cells[$colonnes['date']    ?? 0] ?? '',
                'libelle' => $cells[$colonnes['libelle'] ?? 1] ?? '',
                'montant' => $cells[$colonnes['montant'] ?? 2] ?? '0',
            ];
        })->filter();
    }

    // ── Suggestions intelligentes ────────────────────────────────

    /**
     * Recherche un tiers connu dont le nom apparaît dans le libellé.
     */
    private function suggererTiers(string $libelle, Societe $societe): ?array
    {
        $libelleLower = mb_strtolower($libelle);

        $tiers = Tiers::where('societe_id', $societe->id)
            ->where('actif', true)
            ->get(['id', 'nom', 'type']);

        foreach ($tiers as $t) {
            $mots = array_filter(explode(' ', mb_strtolower($t->nom)));
            foreach ($mots as $mot) {
                if (strlen($mot) > 3 && str_contains($libelleLower, $mot)) {
                    return ['id' => $t->id, 'nom' => $t->nom, 'type' => $t->type];
                }
            }
        }

        return null;
    }

    /**
     * Suggère un numéro de compte comptable basé sur des mots-clés
     * courants dans les libellés de relevé (heuristique simple).
     */
    private function suggererCompte(string $libelle, float $montant, Societe $societe): ?string
    {
        $l = mb_strtolower($libelle);

        // Virements entrants clients
        if ($montant > 0 && (str_contains($l, 'vrt') || str_contains($l, 'virement'))) return '411';

        // Règles par mots-clés
        $regles = [
            '621' => ['loyer', 'bail', 'location'],
            '626' => ['abonnement', 'subscription', 'orange', 'mtn', 'moov', 'internet'],
            '631' => ['frais bancaires', 'commission', 'agios', 'tenue de compte'],
            '641' => ['salaire', 'paie', 'remuneration', 'virement salaire'],
            '625' => ['assurance', 'prime'],
            '628' => ['telephone', 'mobile', 'tel'],
            '614' => ['carburant', 'essence', 'transport'],
            '401' => ['facture', 'règlement fournisseur'],
            '411' => ['client', 'règlement client', 'encaissement'],
        ];

        foreach ($regles as $compte => $mots) {
            foreach ($mots as $mot) {
                if (str_contains($l, $mot)) return $compte;
            }
        }

        // Fallback : charges à classer (montant négatif) ou produits à classer
        return $montant < 0 ? '618' : '708';
    }

    // ── Utilitaires ──────────────────────────────────────────────

    private function detecterSeparateur(string $ligne): string
    {
        $scores = [';' => 0, ',' => 0, "\t" => 0];
        foreach (array_keys($scores) as $sep) {
            $scores[$sep] = substr_count($ligne, $sep);
        }
        return array_key_first(array_filter($scores)) ?: ',';
    }

    private function normaliserDate(string $date): ?string
    {
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd.m.Y', 'Y/m/d'];
        foreach ($formats as $f) {
            $d = \DateTime::createFromFormat($f, trim($date));
            if ($d) return $d->format('Y-m-d');
        }
        return null;
    }

    private function normaliserMontant(string $montant): float
    {
        // Supprimer espaces, remplacer virgule décimale par point
        $m = str_replace([' ', "\u{00A0}", ' '], '', $montant);
        $m = str_replace(',', '.', $m);
        $m = preg_replace('/[^\d.\-+]/', '', $m);
        return (float) $m;
    }

    private function ajouterLignes(Ecriture $ecriture, Societe $societe, string $debit, string $credit, float $montant, string $libelle): void
    {
        foreach ([[$debit, $montant, 0], [$credit, 0, $montant]] as [$num, $d, $c]) {
            $compte = Compte::where('societe_id', $societe->id)
                ->where('numero', $num)
                ->orWhere('numero', 'LIKE', substr($num, 0, 3) . '%')
                ->orderBy('numero')->first();

            if (!$compte) continue;

            \App\Models\LigneEcriture::create([
                'ecriture_id' => $ecriture->id,
                'compte_id'   => $compte->id,
                'libelle'     => $libelle,
                'debit'       => round($d, 2),
                'credit'      => round($c, 2),
                'ordre'       => $d > 0 ? 1 : 2,
            ]);
        }
    }
}
