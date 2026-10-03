<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Vérifie que l'utilisateur connecté a le droit d'accéder à la page
 * courante, selon les permissions de son rôle (voir Role::peutAccederA).
 *
 * Usage dans les routes : Route::middleware('page:tiers')->group(...)
 * L'admin n'est JAMAIS bloqué par ce middleware, quel que soit le
 * paramètre — c'est vérifié à l'intérieur de User::peutAccederA().
 */
class VerifierAccesPage
{
    public function handle(Request $request, Closure $next, string $page)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if (!$user->actif) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez votre administrateur.',
            ]);
        }

        if (!$user->peutAccederA($page)) {
            abort(403, "L'accès à cette page (\"" . (\App\Models\Role::PAGES[$page] ?? $page) . "\") a été restreint par votre administrateur.");
        }

        return $next($request);
    }
}
