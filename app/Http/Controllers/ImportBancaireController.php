<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use App\Services\EcritureService;
use App\Services\ImportBancaireService;
use Illuminate\Http\Request;

class ImportBancaireController extends Controller
{
    public function __construct(
        private ImportBancaireService $importService,
        private EcritureService       $ecritureService,
    ) {}

    /**
     * Formulaire d'upload du relevé bancaire
     */
    public function create()
    {
        $societe  = auth()->user()->societe;
        $comptes  = Compte::where('societe_id', $societe->id)
            ->where('classe', '5')
            ->orderBy('numero')
            ->get(['id', 'numero', 'libelle']);

        return view('import.create', compact('comptes'));
    }

    /**
     * Parse le fichier uploadé et retourne une prévisualisation
     * des opérations détectées pour validation par le comptable.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fichier'         => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
            'compte_releve'   => 'required|string',
        ]);

        $societe  = auth()->user()->societe;
        $lignes   = $this->importService->parser($request->file('fichier'), $societe);

        // Stocker temporairement en session pour la confirmation
        session([
            'import_lignes'  => $lignes->toArray(),
            'import_compte'  => $request->compte_releve,
        ]);

        $comptes = Compte::where('societe_id', $societe->id)
            ->orderBy('numero')
            ->get(['numero', 'libelle'])
            ->mapWithKeys(fn($c) => [$c->numero => $c->numero . ' — ' . $c->libelle]);

        return view('import.preview', [
            'lignes'        => $lignes,
            'compteReleve'  => $request->compte_releve,
            'comptes'       => $comptes,
        ]);
    }

    /**
     * Confirme l'import et crée les écritures en brouillon.
     */
    public function confirmer(Request $request)
    {
        $request->validate([
            'lignes'   => 'required|array',
            'lignes.*.a_importer'       => 'boolean',
            'lignes.*.date'             => 'required|date',
            'lignes.*.libelle'          => 'required|string',
            'lignes.*.montant'          => 'required|numeric',
            'lignes.*.compte_suggere'   => 'nullable|string',
        ]);

        $societe = auth()->user()->societe;
        $compte  = session('import_compte');

        $nb = $this->importService->importer(
            $request->lignes,
            $compte,
            $societe,
            $this->ecritureService
        );

        session()->forget(['import_lignes', 'import_compte']);

        return redirect()->route('ecritures.index')
            ->with('success', "{$nb} écriture(s) importée(s) en brouillon. Vérifiez et validez-les dans le journal.");
    }
}
