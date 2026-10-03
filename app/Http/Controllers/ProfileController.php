<?php

namespace App\Http\Controllers;

use App\Models\DeclarationTva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Page individuelle de l'utilisateur connecté :
     * informations de profil + son activité personnelle
     * (écritures saisies, factures créées, écritures en attente).
     */
    public function show()
    {
        $user = auth()->user()->loadCount(['ecritures', 'factures']);

        $ecrituresValidees  = $user->ecritures()->where('statut', 'validee')->count();
        $ecrituresBrouillon = $user->ecritures()->where('statut', 'brouillon')->count();

        $dernieresEcritures = $user->ecritures()
            ->with('lignes')
            ->latest('date_ecriture')
            ->take(8)
            ->get();

        $dernieresFactures = $user->factures()
            ->with('tiers')
            ->latest('date_emission')
            ->take(8)
            ->get();

        // Montant total facturé par cet utilisateur (factures clients)
        $totalFacture = $user->factures()
            ->where('type', 'client')
            ->whereNotIn('statut', ['brouillon', 'annulee'])
            ->sum('montant_ttc');

        return view('profile.show', [
            'user'               => $user,
            'ecrituresValidees'  => $ecrituresValidees,
            'ecrituresBrouillon' => $ecrituresBrouillon,
            'dernieresEcritures' => $dernieresEcritures,
            'dernieresFactures'  => $dernieresFactures,
            'totalFacture'       => $totalFacture,
        ]);
    }

    /**
     * Mettre à jour les informations personnelles (nom, prénom, email)
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom'    => ['required', 'string', 'max:100'],
            'email'  => ['required', 'email', 'max:191', 'unique:users,email,' . $user->id],
        ]);

        $user->update($data);

        return back()->with('success', 'Vos informations ont été mises à jour.');
    }

    /**
     * Changer son propre mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'          => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Votre mot de passe a été changé.');
    }
}
