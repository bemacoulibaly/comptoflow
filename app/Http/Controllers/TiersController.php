<?php

namespace App\Http\Controllers;

use App\Models\Tiers;
use Illuminate\Http\Request;

class TiersController extends Controller
{
    public function index(Request $request)
    {
        $societe = auth()->user()->societe;

        $tiers = Tiers::where('societe_id', $societe->id)
            ->when($request->type,   fn($q) => $q->where('type', $request->type))
            ->when($request->search, fn($q) => $q->where('nom', 'like', "%{$request->search}%"))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        return view('tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('tiers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Tiers::class);

        $societe = auth()->user()->societe;

        $data = $request->validate([
            'type'                => 'required|in:client,fournisseur,les_deux',
            'nom'                 => 'required|string|max:191',
            'email'               => 'nullable|email|max:191',
            'telephone'           => 'nullable|string|max:30',
            'adresse'             => 'nullable|string|max:255',
            'ville'               => 'nullable|string|max:100',
            'numero_contribuable' => 'nullable|string|max:50',
        ]);

        $tiers = Tiers::create(array_merge($data, ['societe_id' => $societe->id]));

        return redirect()->route('tiers.show', $tiers)
            ->with('success', 'Tiers créé avec succès.');
    }

    public function show(Tiers $tiers)
    {
        $this->authorize('view', $tiers);
        $tiers->load('factures');
        return view('tiers.show', compact('tiers'));
    }

    public function edit(Tiers $tiers)
    {
        $this->authorize('update', $tiers);
        return view('tiers.edit', compact('tiers'));
    }

    public function update(Request $request, Tiers $tiers)
    {
        $this->authorize('update', $tiers);
        $data = $request->validate([
            'type'                => 'required|in:client,fournisseur,les_deux',
            'nom'                 => 'required|string|max:191',
            'email'               => 'nullable|email|max:191',
            'telephone'           => 'nullable|string|max:30',
            'adresse'             => 'nullable|string|max:255',
            'ville'               => 'nullable|string|max:100',
            'numero_contribuable' => 'nullable|string|max:50',
        ]);
        $tiers->update($data);
        return back()->with('success', 'Tiers mis à jour.');
    }

    public function destroy(Tiers $tiers)
    {
        $this->authorize('delete', $tiers);
        $tiers->delete();
        return redirect()->route('tiers.index')->with('success', 'Tiers supprimé.');
    }
}
