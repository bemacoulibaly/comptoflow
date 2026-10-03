<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParametresController extends Controller
{
    public function index()
    {
        $societe   = auth()->user()->societe;
        $nbUsers   = $societe->users()->count();

        return view('parametres.index', compact('societe', 'nbUsers'));
    }

    public function updateSociete(Request $request)
    {
        $societe = auth()->user()->societe;
        $this->authorize('update', $societe);

        $data = $request->validate([
            'raison_sociale'      => 'required|string|max:191',
            'numero_contribuable' => 'required|string|max:50',
            'regime_fiscal'       => 'required|in:reel_simplifie,reel_normal,forfait',
            'devise'              => 'required|string|max:10',
            'taux_tva'            => 'required|numeric|min:0|max:100',
            'adresse'             => 'nullable|string|max:255',
            'ville'               => 'nullable|string|max:100',
            'telephone'           => 'nullable|string|max:30',
            'email'               => 'nullable|email|max:191',
        ]);

        $societe->update($data);
        return back()->with('success', 'Paramètres de la société mis à jour.');
    }
}
