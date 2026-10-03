<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEcritureRequest;
use App\Models\Compte;
use App\Models\Ecriture;
use App\Services\EcritureService;
use Illuminate\Http\Request;

class EcritureController extends Controller
{
    public function __construct(private EcritureService $service) {}

    public function index(Request $request)
    {
        $societe = auth()->user()->societe;

        $ecritures = Ecriture::where('societe_id', $societe->id)
            ->with('user')
            ->when($request->journal,  fn($q) => $q->journal($request->journal))
            ->when($request->statut,   fn($q) => $q->where('statut', $request->statut))
            ->when($request->debut,    fn($q) => $q->where('date_ecriture', '>=', $request->debut))
            ->when($request->fin,      fn($q) => $q->where('date_ecriture', '<=', $request->fin))
            ->when($request->search,   fn($q) => $q->where('libelle','like',"%{$request->search}%"))
            ->orderByDesc('date_ecriture')
            ->paginate(20)
            ->withQueryString();

        return view('ecritures.index', compact('ecritures'));
    }

    public function create()
    {
        $societe = auth()->user()->societe;
        $journal = request('journal', 'BQ');
        $numero  = $this->service->genererNumeroPiece($societe, $journal);
        $comptes = Compte::where('societe_id', $societe->id)->actif()->orderBy('numero')->get();

        return view('ecritures.create', compact('numero', 'comptes', 'journal'));
    }

    public function store(StoreEcritureRequest $request)
    {
        $societe  = auth()->user()->societe;
        $ecriture = $this->service->creer($societe, $request->validated());

        return redirect()->route('ecritures.show', $ecriture)
            ->with('success', 'Écriture créée avec succès.');
    }

    public function show(Ecriture $ecriture)
    {
        $this->authorize('view', $ecriture);
        $ecriture->load('lignes.compte', 'lignes.tiers', 'user');
        return view('ecritures.show', compact('ecriture'));
    }

    public function valider(Ecriture $ecriture)
    {
        $this->authorize('update', $ecriture);

        try {
            $this->service->valider($ecriture);
            return back()->with('success', 'Écriture validée avec succès.');
        } catch (\DomainException $e) {
            return back()->withErrors(['validation' => $e->getMessage()]);
        }
    }

    public function grandLivre(Request $request)
    {
        $request->validate([
            'compte' => 'required|string',
            'debut'  => 'required|date',
            'fin'    => 'required|date|after_or_equal:debut',
        ]);

        $societe = auth()->user()->societe;
        $data    = $this->service->grandLivre($societe, $request->compte, $request->debut, $request->fin);

        return view('ecritures.grand-livre', $data + ['debut' => $request->debut, 'fin' => $request->fin]);
    }
}
