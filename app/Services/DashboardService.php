<?php

namespace App\Services;

use App\Models\Ecriture;
use App\Models\Evenement;
use App\Models\Facture;
use App\Models\Societe;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function getStats(Societe $societe, string $periode = 'mois'): array
    {
        [$debut, $fin] = $this->getPeriode($periode);

        $recettes  = Facture::where('societe_id', $societe->id)->clients()
            ->whereBetween('date_emission', [$debut, $fin])
            ->whereNotIn('statut', ['brouillon','annulee'])->sum('montant_ht');

        $charges   = Facture::where('societe_id', $societe->id)->fournisseurs()
            ->whereBetween('date_emission', [$debut, $fin])
            ->whereNotIn('statut', ['brouillon','annulee'])->sum('montant_ht');

        $tresorerie = Ecriture::where('societe_id', $societe->id)
            ->validees()
            ->whereHas('lignes.compte', fn($q) => $q->whereIn('numero', ['512', '570']))
            ->get()
            ->flatMap->lignes
            ->filter(fn($l) => in_array($l->compte->numero ?? '', ['512','570']))
            ->reduce(fn($carry, $l) => $carry + $l->debit - $l->credit, 0.0);

        $enRetard  = Facture::where('societe_id', $societe->id)->enRetard()->count();
        $aValider  = Ecriture::where('societe_id', $societe->id)->where('statut','brouillon')->count();

        $tva = Facture::where('societe_id', $societe->id)
            ->clients()->whereBetween('date_emission', [$debut, $fin])
            ->whereNotIn('statut', ['brouillon','annulee'])->sum('montant_tva')
            - Facture::where('societe_id', $societe->id)
            ->fournisseurs()->whereBetween('date_emission', [$debut, $fin])
            ->whereNotIn('statut', ['brouillon','annulee'])->sum('montant_tva');

        $echeances = Evenement::where('societe_id', $societe->id)
            ->aVenir()->orderBy('date_echeance')->take(5)->get();

        $evolutionMensuelle = $this->getEvolutionMensuelle($societe);

        return compact(
            'recettes', 'charges', 'tresorerie',
            'enRetard', 'aValider', 'tva',
            'echeances', 'evolutionMensuelle',
        ) + ['resultat' => $recettes - $charges];
    }

    private function getEvolutionMensuelle(Societe $societe): array
    {
        $mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $f = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();
            $label = Carbon::now()->subMonths($i)->translatedFormat('M');

            $r = Facture::where('societe_id', $societe->id)->clients()
                ->whereBetween('date_emission',[$d,$f])->whereNotIn('statut',['brouillon','annulee'])->sum('montant_ht');
            $c = Facture::where('societe_id', $societe->id)->fournisseurs()
                ->whereBetween('date_emission',[$d,$f])->whereNotIn('statut',['brouillon','annulee'])->sum('montant_ht');

            $mois[] = ['label' => $label, 'recettes' => $r, 'charges' => $c];
        }
        return $mois;
    }

    private function getPeriode(string $periode): array
    {
        return match($periode) {
            'semaine' => [Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()],
            'trimestre' => [Carbon::now()->startOfQuarter()->toDateString(), Carbon::now()->endOfQuarter()->toDateString()],
            'annee'  => [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfYear()->toDateString()],
            default  => [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()],
        };
    }
}
