<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        $societe   = auth()->user()->societe;
        $mois      = $request->get('mois', now()->month);
        $annee     = $request->get('annee', now()->year);

        $debut     = \Carbon\Carbon::createFromDate($annee, $mois, 1)->startOfMonth();
        $fin       = $debut->copy()->endOfMonth();

        $evenements = Evenement::where('societe_id', $societe->id)
            ->whereBetween('date_echeance', [$debut->toDateString(), $fin->toDateString()])
            ->orderBy('date_echeance')
            ->get()
            ->groupBy(fn($e) => $e->date_echeance->day);

        $aVenir = Evenement::where('societe_id', $societe->id)
            ->aVenir()->orderBy('date_echeance')->take(8)->get();

        return view('calendrier.index', compact('evenements', 'aVenir', 'debut', 'mois', 'annee'));
    }

    public function store(Request $request)
    {
        $societe = auth()->user()->societe;
        $data = $request->validate([
            'titre'         => 'required|string|max:191',
            'description'   => 'nullable|string',
            'date_echeance' => 'required|date',
            'type'          => 'required|in:tva,cnps,bic,facture,rapprochement,autre',
            'priorite'      => 'required|in:haute,normale,basse',
        ]);

        Evenement::create(array_merge($data, [
            'societe_id' => $societe->id,
            'user_id'    => auth()->id(),
        ]));

        return back()->with('success', 'Échéance ajoutée.');
    }

    public function destroy(Evenement $evenement)
    {
        $this->authorize('delete', $evenement);
        $evenement->delete();
        return back()->with('success', 'Échéance supprimée.');
    }
}
