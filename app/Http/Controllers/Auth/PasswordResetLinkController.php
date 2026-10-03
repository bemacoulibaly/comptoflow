<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Message volontairement identique que le compte existe ou non,
        // pour ne pas révéler quelles adresses email sont enregistrées
        // sur la plateforme (énumération de comptes).
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Si cette adresse est associée à un compte, un lien de réinitialisation vient de lui être envoyé.');
    }
}
