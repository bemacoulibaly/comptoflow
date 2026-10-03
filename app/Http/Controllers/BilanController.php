<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use App\Models\LigneEcriture;
use Illuminate\Http\Request;

class BilanController extends Controller
{
    public function index(Request $request)
    {
        $societe = auth()->user()->societe;
        $annee   = $request->get('annee', now()->year);
        $debut   = "{$annee}-01-01";
        $fin     = "{$annee}-12-31";

        $comptes = Compte::where('societe_id', $societe->id)
            ->actif()
            ->with(['lignes' => fn($q) => $q->whereHas('ecriture',
                fn($e) => $e->validees()->whereBetween('date_ecriture', [$debut, $fin])
            )])
            ->orderBy('numero')
            ->get();

        // Regrouper par classe OHADA
        $actif   = $comptes->filter(fn($c) => in_array($c->classe, ['1','2','3','4','5']) && $c->type === 'actif');
        $passif  = $comptes->filter(fn($c) => in_array($c->classe, ['1','2','3','4','5']) && $c->type === 'passif');
        $charges = $comptes->filter(fn($c) => $c->classe === '6');
        $produits= $comptes->filter(fn($c) => $c->classe === '7');

        $totalActif   = $actif->sum('solde');
        $totalPassif  = $passif->sum('solde');
        $totalCharges = $charges->sum('solde');
        $totalProduits= $produits->sum('solde');
        $resultatNet  = $totalProduits - $totalCharges;

        return view('bilan.index', compact(
            'actif','passif','charges','produits',
            'totalActif','totalPassif','totalCharges','totalProduits',
            'resultatNet','annee'
        ));
    }

    public function resultat(Request $request)
    {
        $societe = auth()->user()->societe;
        $annee   = $request->get('annee', now()->year);
        $debut   = "{$annee}-01-01";
        $fin     = "{$annee}-12-31";

        $comptes = Compte::where('societe_id', $societe->id)
            ->actif()
            ->whereIn('classe', ['6','7'])
            ->with(['lignes' => fn($q) => $q->whereHas('ecriture',
                fn($e) => $e->validees()->whereBetween('date_ecriture', [$debut, $fin])
            )])
            ->orderBy('numero')
            ->get();

        $charges = $comptes->filter(fn($c) => $c->classe === '6');
        $produits= $comptes->filter(fn($c) => $c->classe === '7');

        $totalCharges = $charges->sum('solde');
        $totalProduits= $produits->sum('solde');
        $resultatBrut = $totalProduits - $totalCharges;
        $bic          = max(0, round($resultatBrut * 0.25, 2)); // BIC 25% CI
        $resultatNet  = $resultatBrut - $bic;

        return view('bilan.resultat', compact(
            'charges','produits','totalCharges','totalProduits',
            'resultatBrut','bic','resultatNet','annee'
        ));
    }
}
