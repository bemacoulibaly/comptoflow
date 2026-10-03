<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use App\Models\LigneRapprochement;
use App\Models\Rapprochement;
use App\Services\RapprochementService;
use Illuminate\Http\Request;

class RapprochementController extends Controller
{
    public function __construct(private RapprochementService $service) {}

    public function index()
    {
        $societe        = auth()->user()->societe;
        $rapprochements = Rapprochement::where('societe_id', $societe->id)
            ->with('compte')
            ->orderByDesc('date_fin')
            ->paginate(15);

        return view('rapprochement.index', compact('rapprochements'));
    }

    public function create()
    {
        $societe = auth()->user()->societe;
        $comptes = Compte::where('societe_id', $societe->id)
            ->actif()->whereIn('classe', ['5'])->orderBy('numero')->get();

        return view('rapprochement.create', compact('comptes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_compte' => 'required|string',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after_or_equal:date_debut',
            'solde_releve'  => 'required|numeric',
        ]);

        $societe        = auth()->user()->societe;
        $rapprochement  = $this->service->creer($societe, $request->all());

        return redirect()->route('rapprochement.show', $rapprochement)
            ->with('success', 'Rapprochement créé.');
    }

    public function show(Rapprochement $rapprochement)
    {
        $this->authorize('view', $rapprochement);
        $rapprochement->load('lignes', 'compte');
        return view('rapprochement.show', compact('rapprochement'));
    }

    public function pointer(Request $request, LigneRapprochement $ligne)
    {
        $pointe = $request->boolean('pointe');
        $this->service->pointer($ligne, $pointe);
        return response()->json(['ok' => true, 'ecart' => $ligne->rapprochement->fresh()->ecart]);
    }

    /**
     * Retourne les suggestions automatiques de rapprochement (JSON).
     */
    public function suggestions(Rapprochement $rapprochement)
    {
        $this->authorize('view', $rapprochement);
        $suggestions = $this->service->suggererRapprochements($rapprochement);
        return response()->json($suggestions);
    }

    /**
     * Applique une suggestion : pointe les deux lignes en une action.
     */
    public function appliquerSuggestion(Request $request, Rapprochement $rapprochement)
    {
        $this->authorize('update', $rapprochement);
        $request->validate([
            'releve_id' => 'required|integer',
            'compta_id' => 'required|integer',
        ]);

        $this->service->appliquerSuggestion(
            $rapprochement,
            $request->releve_id,
            $request->compta_id
        );

        $ecart = $rapprochement->fresh()->ecart;
        return response()->json(['ok' => true, 'ecart' => $ecart]);
    }

    public function valider(Rapprochement $rapprochement)
    {
        $this->authorize('update', $rapprochement);
        try {
            $this->service->valider($rapprochement);
            return back()->with('success', 'Rapprochement validé.');
        } catch (\DomainException $e) {
            return back()->withErrors(['ecart' => $e->getMessage()]);
        }
    }
}
