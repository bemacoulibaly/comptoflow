<?php

namespace App\Services;

use App\Models\Compte;
use App\Models\Ecriture;
use App\Models\Facture;
use App\Models\LigneEcriture;
use App\Models\LigneFacture;
use App\Models\Societe;
use Illuminate\Support\Facades\DB;

class FactureService
{
    public function __construct(private EcritureService $ecritureService) {}

    public function creer(Societe $societe, array $data): Facture
    {
        return DB::transaction(function () use ($societe, $data) {
            $numero = $this->genererNumero($societe, $data['type']);

            $facture = Facture::create([
                'societe_id'    => $societe->id,
                'tiers_id'      => $data['tiers_id'],
                'user_id'       => auth()->id(),
                'numero'        => $numero,
                'type'          => $data['type'],
                'date_emission' => $data['date_emission'],
                'date_echeance' => $data['date_echeance'],
                'taux_tva'      => $data['taux_tva'] ?? $societe->taux_tva,
                'notes'         => $data['notes'] ?? null,
                'statut'        => 'brouillon',
                'montant_ht'    => 0,
                'montant_tva'   => 0,
                'montant_ttc'   => 0,
                'montant_paye'  => 0,
            ]);

            foreach ($data['lignes'] as $i => $l) {
                LigneFacture::create([
                    'facture_id'    => $facture->id,
                    'designation'   => $l['designation'],
                    'quantite'      => $l['quantite'],
                    'unite'         => $l['unite'] ?? null,
                    'prix_unitaire' => $l['prix_unitaire'],
                    'taux_tva'      => $l['taux_tva'] ?? $societe->taux_tva,
                    'ordre'         => $i,
                ]);
            }

            $facture->recalculerTotaux();
            $facture = $facture->load('lignes', 'tiers');

            // Générer automatiquement l'écriture comptable SYSCOHADA
            $ecriture = $this->genererEcritureFacture($societe, $facture);
            $facture->update(['ecriture_id' => $ecriture->id]);

            return $facture;
        });
    }

    /**
     * Génère l'écriture comptable lors de l'enregistrement d'un paiement.
     *
     * Facture CLIENT payée (encaissement) :
     *   Débit  512 Banque       TTC
     *   Crédit 411 Clients      TTC
     *
     * Facture FOURNISSEUR payée (décaissement) :
     *   Débit  401 Fournisseurs TTC
     *   Crédit 512 Banque       TTC
     */
    public function enregistrerPaiement(Facture $facture, float $montant, string $compteReglement = '512'): Facture
    {
        return DB::transaction(function () use ($facture, $montant, $compteReglement) {
            $facture->montant_paye += $montant;

            $facture->statut = match (true) {
                $facture->montant_paye >= $facture->montant_ttc => 'payee',
                $facture->montant_paye > 0                      => 'partielle',
                default                                          => $facture->statut,
            };

            $facture->save();

            // Écriture automatique de règlement
            $this->genererEcriturePaiement($facture->societe, $facture, $montant, $compteReglement);

            return $facture->fresh();
        });
    }

    public function verifierRetards(Societe $societe): int
    {
        return Facture::where('societe_id', $societe->id)
            ->enRetard()
            ->update(['statut' => 'en_retard']);
    }

    public function genererNumero(Societe $societe, string $type): string
    {
        $prefix = $type === 'client' ? 'FAC' : 'FF';
        $annee  = now()->format('Y');
        $count  = Facture::where('societe_id', $societe->id)
            ->where('type', $type)
            ->whereYear('date_emission', $annee)
            ->count();

        return sprintf('%s-%s-%04d', $prefix, $annee, $count + 1);
    }

    public function statsParPeriode(Societe $societe, string $debut, string $fin): array
    {
        $clients = Facture::where('societe_id', $societe->id)
            ->clients()
            ->whereBetween('date_emission', [$debut, $fin]);

        $fournisseurs = Facture::where('societe_id', $societe->id)
            ->fournisseurs()
            ->whereBetween('date_emission', [$debut, $fin]);

        return [
            'recettes_ht'        => $clients->sum('montant_ht'),
            'recettes_ttc'       => $clients->sum('montant_ttc'),
            'charges_ht'         => $fournisseurs->sum('montant_ht'),
            'charges_ttc'        => $fournisseurs->sum('montant_ttc'),
            'factures_en_retard' => Facture::where('societe_id', $societe->id)->enRetard()->count(),
            'tva_collectee'      => $clients->sum('montant_tva'),
            'tva_deductible'     => $fournisseurs->sum('montant_tva'),
        ];
    }

    // ── Génération des écritures comptables ─────────────────────────────

    /**
     * Écriture de facturation (VT pour vente, AC pour achat) :
     *
     * Facture CLIENT :
     *   Débit  411x Clients          TTC
     *   Crédit 706  Prestations HT   HT
     *   Crédit 4412 TVA collectée    TVA
     *
     * Facture FOURNISSEUR :
     *   Débit  604  Achats           HT
     *   Débit  4451 TVA déductible   TVA
     *   Crédit 401  Fournisseurs     TTC
     */
    private function genererEcritureFacture(Societe $societe, Facture $facture): Ecriture
    {
        $journal = $facture->type === 'client' ? 'VT' : 'AC';
        $libelle = ($facture->type === 'client' ? 'Facture client ' : 'Facture fournisseur ') . $facture->numero;
        $numero  = $this->ecritureService->genererNumeroPiece($societe, $journal);

        $ecriture = Ecriture::create([
            'societe_id'      => $societe->id,
            'user_id'         => auth()->id(),
            'numero_piece'    => $numero,
            'date_ecriture'   => $facture->date_emission,
            'journal'         => $journal,
            'libelle'         => $libelle,
            'reference_tiers' => $facture->tiers->nom,
            'statut'          => 'brouillon',
        ]);

        $ht  = $facture->montant_ht;
        $tva = $facture->montant_tva;
        $ttc = $facture->montant_ttc;

        if ($facture->type === 'client') {
            $this->ajouterLigne($ecriture, $societe, '411',  $facture->tiers->nom, $ttc, 0.00, 1);
            $this->ajouterLigne($ecriture, $societe, '706',  'Prestations de services HT', 0.00, $ht, 2);
            $this->ajouterLigne($ecriture, $societe, '4412', 'TVA collectée 18%', 0.00, $tva, 3);
        } else {
            $this->ajouterLigne($ecriture, $societe, '604',  'Achats de services HT', $ht, 0.00, 1);
            $this->ajouterLigne($ecriture, $societe, '4451', 'TVA déductible 18%', $tva, 0.00, 2);
            $this->ajouterLigne($ecriture, $societe, '401',  $facture->tiers->nom, 0.00, $ttc, 3);
        }

        return $ecriture;
    }

    /**
     * Écriture de règlement (BQ) — encaissement ou décaissement.
     */
    private function genererEcriturePaiement(Societe $societe, Facture $facture, float $montant, string $compteReglement): void
    {
        $numero  = $this->ecritureService->genererNumeroPiece($societe, 'BQ');
        $libelle = 'Règlement ' . $facture->numero . ' — ' . $facture->tiers->nom;

        $ecriture = Ecriture::create([
            'societe_id'      => $societe->id,
            'user_id'         => auth()->id(),
            'numero_piece'    => $numero,
            'date_ecriture'   => now()->toDateString(),
            'journal'         => 'BQ',
            'libelle'         => $libelle,
            'reference_tiers' => $facture->tiers->nom,
            'statut'          => 'brouillon',
        ]);

        if ($facture->type === 'client') {
            // Encaissement : Banque ↑, Client ↓
            $this->ajouterLigne($ecriture, $societe, $compteReglement, 'Encaissement ' . $facture->numero, $montant, 0.00, 1);
            $this->ajouterLigne($ecriture, $societe, '411', $facture->tiers->nom, 0.00, $montant, 2);
        } else {
            // Décaissement : Fournisseur ↓, Banque ↓
            $this->ajouterLigne($ecriture, $societe, '401', $facture->tiers->nom, $montant, 0.00, 1);
            $this->ajouterLigne($ecriture, $societe, $compteReglement, 'Paiement ' . $facture->numero, 0.00, $montant, 2);
        }
    }

    private function ajouterLigne(Ecriture $ecriture, Societe $societe, string $numeroCompte, string $libelle, float $debit, float $credit, int $ordre): void
    {
        $compte = Compte::where('societe_id', $societe->id)
            ->where('numero', $numeroCompte)
            ->first();

        if (!$compte) {
            // Si le compte précis n'existe pas, chercher le compte parent le plus proche
            $compte = Compte::where('societe_id', $societe->id)
                ->where('numero', 'LIKE', substr($numeroCompte, 0, 3) . '%')
                ->orderBy('numero')
                ->first();
        }

        if (!$compte) return; // Ne jamais bloquer si le compte est introuvable

        LigneEcriture::create([
            'ecriture_id' => $ecriture->id,
            'compte_id'   => $compte->id,
            'libelle'     => $libelle,
            'debit'       => round($debit, 2),
            'credit'      => round($credit, 2),
            'ordre'       => $ordre,
        ]);
    }
}
