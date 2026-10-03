<?php

namespace App\Services;

use App\Models\Compte;
use App\Models\Ecriture;
use App\Models\LigneEcriture;
use App\Models\Societe;
use Illuminate\Support\Facades\DB;

class EcritureService
{
    public function creer(Societe $societe, array $data): Ecriture
    {
        return DB::transaction(function () use ($societe, $data) {
            $ecriture = Ecriture::create([
                'societe_id'      => $societe->id,
                'user_id'         => auth()->id(),
                'numero_piece'    => $data['numero_piece'],
                'date_ecriture'   => $data['date_ecriture'],
                'journal'         => $data['journal'],
                'libelle'         => $data['libelle'],
                'reference_tiers' => $data['reference_tiers'] ?? null,
                'statut'          => 'brouillon',
            ]);

            foreach ($data['lignes'] as $i => $ligne) {
                $compte = Compte::where('societe_id', $societe->id)
                    ->where('numero', $ligne['numero_compte'])
                    ->firstOrFail();

                LigneEcriture::create([
                    'ecriture_id' => $ecriture->id,
                    'compte_id'   => $compte->id,
                    'tiers_id'    => $ligne['tiers_id'] ?? null,
                    'libelle'     => $ligne['libelle'],
                    'debit'       => $ligne['debit'] ?? 0,
                    'credit'      => $ligne['credit'] ?? 0,
                    'ordre'       => $i,
                ]);
            }

            return $ecriture->load('lignes.compte');
        });
    }

    public function valider(Ecriture $ecriture): Ecriture
    {
        return DB::transaction(function () use ($ecriture) {
            if (!$ecriture->isEquilibree()) {
                throw new \DomainException('L\'écriture doit être équilibrée (débit = crédit).');
            }
            $ecriture->valider();
            return $ecriture->fresh('lignes.compte');
        });
    }

    public function genererNumeroPiece(Societe $societe, string $journal): string
    {
        $annee   = now()->format('Y');
        $dernier = Ecriture::where('societe_id', $societe->id)
            ->where('journal', $journal)
            ->whereYear('date_ecriture', $annee)
            ->count();

        return sprintf('%s-%s-%04d', $journal, $annee, $dernier + 1);
    }

    public function grandLivre(Societe $societe, string $numeroCompte, string $debut, string $fin): array
    {
        $compte = Compte::where('societe_id', $societe->id)
            ->where('numero', $numeroCompte)->firstOrFail();

        $lignes = LigneEcriture::with('ecriture')
            ->where('compte_id', $compte->id)
            ->whereHas('ecriture', fn($q) =>
                $q->validees()->whereBetween('date_ecriture', [$debut, $fin])
            )
            ->orderBy('created_at')
            ->get();

        $solde = 0;
        $rows  = $lignes->map(function ($l) use (&$solde, $compte) {
            $solde += in_array($compte->type, ['actif','charge'])
                ? $l->debit - $l->credit
                : $l->credit - $l->debit;

            return [
                'date'    => $l->ecriture->date_ecriture,
                'piece'   => $l->ecriture->numero_piece,
                'libelle' => $l->libelle,
                'debit'   => $l->debit,
                'credit'  => $l->credit,
                'solde'   => $solde,
            ];
        });

        return [
            'compte' => $compte,
            'lignes' => $rows,
            'total_debit'  => $lignes->sum('debit'),
            'total_credit' => $lignes->sum('credit'),
            'solde_final'  => $solde,
        ];
    }
}
