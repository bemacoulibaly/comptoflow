<?php

namespace App\Services;

use App\Models\Compte;
use App\Models\LigneEcriture;
use App\Models\LigneRapprochement;
use App\Models\Rapprochement;
use App\Models\Societe;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RapprochementService
{
    public function creer(Societe $societe, array $data): Rapprochement
    {
        return DB::transaction(function () use ($societe, $data) {
            $compte = Compte::where('societe_id', $societe->id)
                ->where('numero', $data['numero_compte'])
                ->firstOrFail();

            $soldeComptable = LigneEcriture::where('compte_id', $compte->id)
                ->whereHas('ecriture', fn($q) => $q
                    ->validees()
                    ->whereBetween('date_ecriture', [$data['date_debut'], $data['date_fin']])
                )
                ->selectRaw('SUM(debit) - SUM(credit) as solde')
                ->value('solde') ?? 0;

            $rapprochement = Rapprochement::create([
                'societe_id'      => $societe->id,
                'compte_id'       => $compte->id,
                'date_debut'      => $data['date_debut'],
                'date_fin'        => $data['date_fin'],
                'solde_releve'    => $data['solde_releve'],
                'solde_comptable' => $soldeComptable,
                'ecart'           => round($data['solde_releve'] - $soldeComptable, 2),
                'statut'          => 'en_cours',
            ]);

            // Importer les lignes du relevé
            if (!empty($data['lignes_releve'])) {
                foreach ($data['lignes_releve'] as $l) {
                    LigneRapprochement::create([
                        'rapprochement_id' => $rapprochement->id,
                        'date_operation'   => $l['date'],
                        'libelle'          => $l['libelle'],
                        'montant'          => $l['montant'],
                        'source'           => 'releve',
                        'pointe'           => false,
                    ]);
                }
            }

            // Importer les lignes comptables non encore pointées
            $lignesCompta = LigneEcriture::where('compte_id', $compte->id)
                ->whereHas('ecriture', fn($q) => $q
                    ->validees()
                    ->whereBetween('date_ecriture', [$data['date_debut'], $data['date_fin']])
                )
                ->with('ecriture')
                ->get();

            foreach ($lignesCompta as $le) {
                $montant = $le->debit > 0 ? $le->debit : -$le->credit;
                LigneRapprochement::create([
                    'rapprochement_id'  => $rapprochement->id,
                    'ligne_ecriture_id' => $le->id,
                    'date_operation'    => $le->ecriture->date_ecriture,
                    'libelle'           => $le->libelle ?: $le->ecriture->libelle,
                    'montant'           => $montant,
                    'source'            => 'comptabilite',
                    'pointe'            => false,
                ]);
            }

            return $rapprochement->load('lignes');
        });
    }

    /**
     * Suggestion automatique de rapprochements.
     *
     * Pour chaque ligne du relevé non encore pointée, on cherche
     * dans les lignes comptables une correspondance par :
     *   1. Montant identique (±1 FCFA de tolérance d'arrondi)
     *   2. Date proche (±5 jours — délai de traitement bancaire normal)
     *
     * On retourne une liste de paires {releve_id, comptabilite_id, score}
     * triées par score décroissant. Le comptable n'a plus qu'à confirmer.
     */
    public function suggererRapprochements(Rapprochement $rapprochement): Collection
    {
        $rapprochement->load('lignes');

        $releve     = $rapprochement->lignes->where('source', 'releve')->where('pointe', false);
        $comptables = $rapprochement->lignes->where('source', 'comptabilite')->where('pointe', false);

        $suggestions = collect();

        foreach ($releve as $lr) {
            $meilleures = $comptables
                ->filter(function ($lc) use ($lr) {
                    // Montant identique à 1 FCFA près
                    return abs($lr->montant - $lc->montant) <= 1;
                })
                ->map(function ($lc) use ($lr) {
                    // Score basé sur la proximité de date (max 100 si même jour)
                    $ecartJours = abs($lr->date_operation->diffInDays($lc->date_operation));
                    $score = max(0, 100 - ($ecartJours * 20)); // -20 pts/jour d'écart

                    return [
                        'releve_id'      => $lr->id,
                        'releve_libelle' => $lr->libelle,
                        'releve_date'    => $lr->date_operation->format('d/m/Y'),
                        'releve_montant' => $lr->montant,
                        'compta_id'      => $lc->id,
                        'compta_libelle' => $lc->libelle,
                        'compta_date'    => $lc->date_operation->format('d/m/Y'),
                        'ecart_jours'    => $ecartJours,
                        'score'          => $score,
                        'confiance'      => match (true) {
                            $ecartJours === 0 => 'haute',
                            $ecartJours <= 2  => 'moyenne',
                            default           => 'faible',
                        },
                    ];
                })
                ->sortByDesc('score')
                ->first(); // On ne garde que le meilleur match par ligne relevé

            if ($meilleures) {
                $suggestions->push($meilleures);
            }
        }

        return $suggestions->sortByDesc('score')->values();
    }

    /**
     * Appliquer une suggestion : pointe les deux lignes correspondantes
     * (relevé + comptabilité) en une seule action.
     */
    public function appliquerSuggestion(Rapprochement $rapprochement, int $releveId, int $comptaId): void
    {
        DB::transaction(function () use ($rapprochement, $releveId, $comptaId) {
            LigneRapprochement::whereIn('id', [$releveId, $comptaId])
                ->where('rapprochement_id', $rapprochement->id)
                ->update(['pointe' => true]);

            $this->recalculerEcart($rapprochement);
        });
    }

    public function pointer(LigneRapprochement $ligne, bool $pointe): LigneRapprochement
    {
        $ligne->update(['pointe' => $pointe]);
        $this->recalculerEcart($ligne->rapprochement);
        return $ligne->fresh();
    }

    public function valider(Rapprochement $rapprochement): Rapprochement
    {
        if (abs($rapprochement->ecart) > 0.01) {
            throw new \DomainException('Impossible de valider : écart non nul de ' . $rapprochement->ecart . ' FCFA.');
        }
        $rapprochement->update([
            'statut'    => 'valide',
            'valide_at' => now(),
            'valide_par'=> auth()->id(),
        ]);
        return $rapprochement->fresh();
    }

    private function recalculerEcart(Rapprochement $rapprochement): void
    {
        $pointees = $rapprochement->lignes()->where('pointe', true)->sum('montant');
        $rapprochement->update([
            'ecart' => round($rapprochement->solde_releve - $pointees, 2),
        ]);
    }
}
