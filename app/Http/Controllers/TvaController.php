<?php

namespace App\Http\Controllers;

use App\Models\DeclarationTva;
use App\Services\TvaService;
use Illuminate\Http\Request;

class TvaController extends Controller
{
    public function __construct(private TvaService $service) {}

    public function index()
    {
        $societe      = auth()->user()->societe;
        $historique   = $this->service->historique($societe);
        $debutMois    = now()->startOfMonth()->toDateString();
        $finMois      = now()->endOfMonth()->toDateString();
        $apercu       = $this->service->calculer($societe, $debutMois, $finMois);

        return view('tva.index', compact('historique', 'apercu', 'debutMois', 'finMois'));
    }

    public function generer(Request $request)
    {
        $request->validate([
            'periode_debut' => 'required|date',
            'periode_fin'   => 'required|date|after_or_equal:periode_debut',
        ]);

        $societe     = auth()->user()->societe;
        $declaration = $this->service->genererDeclaration(
            $societe,
            $request->periode_debut,
            $request->periode_fin
        );

        return redirect()->route('tva.show', $declaration)
            ->with('success', 'Déclaration TVA générée.');
    }

    public function show(DeclarationTva $declaration)
    {
        $this->authorize('view', $declaration);
        return view('tva.show', compact('declaration'));
    }

    public function valider(DeclarationTva $declaration)
    {
        $this->authorize('update', $declaration);
        $this->service->valider($declaration);
        return back()->with('success', 'Déclaration TVA validée.');
    }
}
