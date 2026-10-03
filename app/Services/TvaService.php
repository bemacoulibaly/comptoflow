<?php

namespace App\Services;

use App\Models\DeclarationTva;
use App\Models\Facture;
use App\Models\Societe;
use Illuminate\Support\Facades\DB;

class TvaService
{
    public function calculer(Societe $societe, string $debut, string $fin): array
    {
        $collectee = Facture::where('societe_id', $societe->id)
            ->clients()
            ->whereBetween('date_emission', [$debut, $fin])
            ->whereNotIn('statut', ['brouillon', 'annulee'])
            ->sum('montant_tva');

        $deductible = Facture::where('societe_id', $societe->id)
            ->fournisseurs()
            ->whereBetween('date_emission', [$debut, $fin])
            ->whereNotIn('statut', ['brouillon', 'annulee'])
            ->sum('montant_tva');

        return [
            'tva_collectee'  => round($collectee, 2),
            'tva_deductible' => round($deductible, 2),
            'tva_nette'      => round($collectee - $deductible, 2),
        ];
    }

    public function genererDeclaration(Societe $societe, string $debut, string $fin): DeclarationTva
    {
        return DB::transaction(function () use ($societe, $debut, $fin) {
            $existing = DeclarationTva::where('societe_id', $societe->id)
                ->where('periode_debut', $debut)
                ->where('periode_fin', $fin)
                ->whereIn('statut', ['brouillon'])
                ->first();

            if ($existing) {
                $existing->delete();
            }

            $totaux = $this->calculer($societe, $debut, $fin);

            return DeclarationTva::create([
                'societe_id'     => $societe->id,
                'periode_debut'  => $debut,
                'periode_fin'    => $fin,
                'tva_collectee'  => $totaux['tva_collectee'],
                'tva_deductible' => $totaux['tva_deductible'],
                'tva_nette'      => $totaux['tva_nette'],
                'statut'         => 'brouillon',
                'date_echeance'  => now()->parse($fin)->endOfMonth()->addDays(15)->toDateString(),
            ]);
        });
    }

    public function valider(DeclarationTva $declaration): DeclarationTva
    {
        $declaration->update([
            'statut'     => 'validee',
            'validee_at' => now(),
        ]);
        return $declaration->fresh();
    }

    public function historique(Societe $societe): \Illuminate\Database\Eloquent\Collection
    {
        return DeclarationTva::where('societe_id', $societe->id)
            ->orderByDesc('periode_debut')
            ->get();
    }
}
