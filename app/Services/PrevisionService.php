<?php

namespace App\Services;

use App\Models\Facture;
use App\Models\Societe;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PrevisionService
{
    /**
     * Calcule une projection de trésorerie sur les 6 prochains mois.
     *
     * Méthode :
     *  — Encaissements attendus = factures clients émises non payées (date échéance)
     *  — Décaissements attendus = factures fournisseurs non payées (date échéance)
     *  — Récurrences            = moyenne des 3 derniers mois (tendance lissée)
     *  — Solde cumulé           = solde actuel + flux nets mois par mois
     */
    public function projections(Societe $societe, float $soldeCourant): array
    {
        $mois = $this->genererMois(6);

        // Factures en attente de règlement
        $facturesClient = Facture::where('societe_id', $societe->id)
            ->where('type', 'client')
            ->whereNotIn('statut', ['payee', 'annulee', 'brouillon'])
            ->select('date_echeance', 'montant_ttc', 'montant_paye')
            ->get();

        $facturesFourn = Facture::where('societe_id', $societe->id)
            ->where('type', 'fournisseur')
            ->whereNotIn('statut', ['payee', 'annulee', 'brouillon'])
            ->select('date_echeance', 'montant_ttc', 'montant_paye')
            ->get();

        // Moyenne mensuelle des 3 derniers mois (récurrences estimées)
        $moyEnc = $this->moyenneMensuelle($societe, 'client',      3);
        $moyDec = $this->moyenneMensuelle($societe, 'fournisseur', 3);

        $solde = $soldeCourant;
        $resultats = [];

        foreach ($mois as $m) {
            $debut = $m->copy()->startOfMonth();
            $fin   = $m->copy()->endOfMonth();

            // Encaissements certains (factures dues ce mois)
            $encCertain = $facturesClient
                ->filter(fn($f) => Carbon::parse($f->date_echeance)->between($debut, $fin))
                ->sum(fn($f) => $f->montant_ttc - $f->montant_paye);

            // Décaissements certains (fournisseurs dus ce mois)
            $decCertain = $facturesFourn
                ->filter(fn($f) => Carbon::parse($f->date_echeance)->between($debut, $fin))
                ->sum(fn($f) => $f->montant_ttc - $f->montant_paye);

            // Pour les mois sans factures connues, on utilise la moyenne historique
            $encTotal = $encCertain > 0 ? $encCertain : $moyEnc;
            $decTotal = $decCertain > 0 ? $decCertain : $moyDec;

            $fluxNet = $encTotal - $decTotal;
            $solde   = $solde + $fluxNet;

            $resultats[] = [
                'label'        => ucfirst($m->translatedFormat('M Y')),
                'mois'         => $m->format('Y-m'),
                'encaissements'=> round($encTotal),
                'decaissements'=> round($decTotal),
                'flux_net'     => round($fluxNet),
                'solde_prevu'  => round($solde),
                'certain'      => $encCertain > 0 || $decCertain > 0,
            ];
        }

        return [
            'projections'  => $resultats,
            'solde_initial'=> round($soldeCourant),
            'tendance'     => $this->calculerTendance($resultats),
        ];
    }

    /**
     * Calcule la moyenne mensuelle des encaissements/décaissements
     * sur les N derniers mois — sert de base de projection pour
     * les mois sans factures connues.
     */
    private function moyenneMensuelle(Societe $societe, string $type, int $nMois): float
    {
        $debut = now()->subMonths($nMois)->startOfMonth();

        $total = Facture::where('societe_id', $societe->id)
            ->where('type', $type)
            ->whereNotIn('statut', ['brouillon', 'annulee'])
            ->where('date_emission', '>=', $debut)
            ->sum('montant_ttc');

        return $nMois > 0 ? round($total / $nMois) : 0;
    }

    private function genererMois(int $n): Collection
    {
        return collect(range(1, $n))->map(
            fn($i) => now()->addMonths($i)->startOfMonth()
        );
    }

    private function calculerTendance(array $resultats): string
    {
        $premier = $resultats[0]['solde_prevu'] ?? 0;
        $dernier = end($resultats)['solde_prevu'] ?? 0;

        if ($dernier > $premier * 1.1) return 'hausse';
        if ($dernier < $premier * 0.9) return 'baisse';
        return 'stable';
    }
}
