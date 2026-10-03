<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Traiter la tentative de connexion.
     * Limité à 5 tentatives par minute, par combinaison email+IP,
     * pour empêcher le bruteforce de mot de passe.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.email'       => 'L\'adresse email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $secondes = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives de connexion. Réessayez dans {$secondes} secondes.",
            ]);
        }

        if (! Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            RateLimiter::hit($throttleKey, 60); // verrou de 60 secondes après le seuil atteint
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects. Vérifiez votre email et mot de passe.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        // Vérifier que l'utilisateur a bien une société
        if (! auth()->user()->societe_id) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Votre compte n\'est associé à aucune société. Contactez l\'administrateur.',
            ]);
        }

        // Bloquer la connexion si le compte a été désactivé par l'admin
        if (! auth()->user()->actif) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Votre compte a été désactivé. Contactez votre administrateur.',
            ]);
        }

        \App\Models\ActivityLog::enregistrer('connexion', 's\'est connecté.');

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Déconnecter l'utilisateur
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
