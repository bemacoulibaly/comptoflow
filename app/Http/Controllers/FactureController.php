<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFactureRequest;
use App\Models\Facture;
use App\Models\Tiers;
use App\Services\FactureService;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function __construct(private FactureService $service) {}

    public function index(Request $request)
    {
        $societe = auth()->user()->societe;

        $factures = Facture::where('societe_id', $societe->id)
            ->with('tiers')
            ->when($request->type,   fn($q) => $q->where('type', $request->type))
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->search, fn($q) => $q->whereHas('tiers',
                fn($t) => $t->where('nom', 'like', "%{$request->search}%")))
            ->orderByDesc('date_emission')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->statsParPeriode(
            $societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        return view('factures.index', compact('factures', 'stats'));
    }

    public function create(Request $request)
    {
        $societe = auth()->user()->societe;
        $type    = $request->get('type', 'client');
        $tiers   = Tiers::where('societe_id', $societe->id)
            ->where('actif', true)
            ->orderBy('nom')->get();

        return view('factures.create', compact('type', 'tiers', 'societe'));
    }

    public function store(StoreFactureRequest $request)
    {
        $societe = auth()->user()->societe;
        $facture = $this->service->creer($societe, $request->validated());

        return redirect()->route('factures.show', $facture)
            ->with('success', "Facture {$facture->numero} créée avec succès.");
    }

    public function show(Facture $facture)
    {
        $this->authorize('view', $facture);
        $facture->load('lignes', 'tiers', 'user', 'ecriture');
        return view('factures.show', compact('facture'));
    }

    public function paiement(Request $request, Facture $facture)
    {
        $this->authorize('update', $facture);
        $request->validate([
            'montant'          => 'required|numeric|min:0.01',
            'compte_reglement' => 'required|in:512,570',
        ]);

        $this->service->enregistrerPaiement(
            $facture,
            $request->montant,
            $request->compte_reglement
        );

        return back()->with('success', 'Paiement enregistré et écriture comptable générée automatiquement.');
    }

    public function annuler(Facture $facture)
    {
        $this->authorize('update', $facture);
        $facture->update(['statut' => 'annulee']);
        return back()->with('success', 'Facture annulée.');
    }
}
