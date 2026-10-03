<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AuditTrail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Toute la ressource est réservée à l'admin de la société.
     * (Pas de HasMiddleware ici : certaines versions de Laravel 11
     * conservent une méthode d'instance middleware() sur la classe
     * parente Illuminate\Routing\Controller, ce qui entre en conflit
     * avec la signature statique exigée par l'interface HasMiddleware.
     * On vérifie donc l'autorisation explicitement à chaque méthode.)
     */
    private function ensureIsAdmin(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, "Seul un administrateur peut gérer les comptes utilisateurs.");
        }
    }

    /**
     * Liste des utilisateurs de la société courante
     */
    public function index()
    {
        $this->ensureIsAdmin();

        $societe = auth()->user()->societe;

        $users = $societe->users()
            ->with('roleAssigne')
            ->withCount(['ecritures', 'factures'])
            ->orderByDesc('actif')
            ->orderBy('nom')
            ->get();

        $roles = Role::where('societe_id', $societe->id)->orderBy('nom')->get();

        return view('parametres.utilisateurs.index', compact('users', 'roles'));
    }

    /**
     * Formulaire de création d'un nouveau compte
     */
    public function create()
    {
        $this->ensureIsAdmin();

        $roles = Role::where('societe_id', auth()->user()->societe_id)->orderBy('nom')->get();

        return view('parametres.utilisateurs.create', compact('roles'));
    }

    /**
     * Créer le compte — l'admin choisit le mot de passe initial
     * (pas d'auto-inscription : l'admin transmet les identifiants)
     */
    public function store(Request $request)
    {
        $this->ensureIsAdmin();

        $societe = auth()->user()->societe;

        $data = $request->validate([
            'prenom'   => ['required', 'string', 'max:100'],
            'nom'      => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:191', 'unique:users,email'],
            'role'     => ['required', 'in:admin,editeur,lecteur'],
            'role_id'  => ['nullable', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.unique' => "Un compte existe déjà avec cette adresse email.",
        ]);

        $user = User::create([
            'prenom'     => $data['prenom'],
            'nom'        => $data['nom'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'role_id'    => $data['role_id'] ?? null,
            'password'   => Hash::make($data['password']),
            'societe_id' => $societe->id,
            'actif'      => true,
        ]);

        ActivityLog::enregistrer(
            'utilisateur.cree',
            "a créé le compte de {$user->nom_complet} ({$user->nom_role_affichage})",
            $user
        );

        return redirect()->route('parametres.utilisateurs.index')
            ->with('success', "Le compte de {$data['prenom']} {$data['nom']} a été créé. Communiquez-lui ses identifiants.");
    }

    /**
     * Page individuelle d'un utilisateur (profil + activité)
     * Visible par l'admin (tout collaborateur) ou par l'utilisateur
     * lui-même via /profil — voir ProfileController.
     */
    public function show(User $user)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($user);

        $user->load('roleAssigne')->loadCount(['ecritures', 'factures']);

        $dernieresEcritures = $user->ecritures()
            ->with('lignes')
            ->latest('date_ecriture')
            ->take(5)
            ->get();

        $dernieresFactures = $user->factures()
            ->with('tiers')
            ->latest('date_emission')
            ->take(5)
            ->get();

        $roles = Role::where('societe_id', auth()->user()->societe_id)->orderBy('nom')->get();

        $derniersLogs = ActivityLog::where('user_id', $user->id)
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('parametres.utilisateurs.show', [
            'user'               => $user,
            'roles'              => $roles,
            'dernieresEcritures' => $dernieresEcritures,
            'dernieresFactures'  => $dernieresFactures,
            'derniersLogs'       => $derniersLogs,
        ]);
    }

    /**
     * Vue complète de l'activité d'un utilisateur :
     * journal textuel + historique avant/après, sans limite de pagination.
     * C'est ici que l'admin "voit tout ce que le compte a pu faire".
     */
    public function activite(Request $request, User $user)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($user);

        $logs = ActivityLog::where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(30, ['*'], 'logs');

        $audits = AuditTrail::where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(30, ['*'], 'audits');

        return view('parametres.utilisateurs.activite', compact('user', 'logs', 'audits'));
    }

    /**
     * Journal d'activité global de toute la société — vue d'ensemble admin.
     */
    public function journal(Request $request)
    {
        $this->ensureIsAdmin();

        $societe = auth()->user()->societe;

        $logs = ActivityLog::where('societe_id', $societe->id)
            ->with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->latest('created_at')
            ->paginate(40);

        $users = $societe->users()->orderBy('nom')->get();

        return view('parametres.journal', compact('logs', 'users'));
    }

    /**
     * Modifier le rôle d'un utilisateur — accepte soit un rôle système
     * (admin/editeur/lecteur, legacy) soit un rôle personnalisé (role_id).
     */
    public function updateRole(Request $request, User $user)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => "Vous ne pouvez pas modifier votre propre rôle."]);
        }

        $data = $request->validate([
            'role'    => ['required', 'in:admin,editeur,lecteur'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        // Si un rôle personnalisé est choisi, vérifier qu'il appartient à la même société
        if (!empty($data['role_id'])) {
            $roleCible = Role::find($data['role_id']);
            if (!$roleCible || $roleCible->societe_id !== auth()->user()->societe_id) {
                abort(403);
            }
        }

        $ancienAffichage = $user->nom_role_affichage;
        $user->update($data);
        $user->refresh();

        ActivityLog::enregistrer(
            'utilisateur.role_modifie',
            "a changé le rôle de {$user->nom_complet} : {$ancienAffichage} → {$user->nom_role_affichage}",
            $user
        );

        return back()->with('success', "Rôle de {$user->nom_complet} mis à jour : {$user->nom_role_affichage}.");
    }

    /**
     * Réinitialiser le mot de passe d'un collaborateur
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($user);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password'       => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ]);

        ActivityLog::enregistrer(
            'utilisateur.mot_de_passe_reinitialise',
            "a réinitialisé le mot de passe de {$user->nom_complet}",
            $user
        );

        return back()->with('success', "Mot de passe de {$user->nom_complet} réinitialisé.");
    }

    /**
     * Activer ou désactiver un compte SANS le supprimer.
     * Un compte désactivé reste entièrement visible et consultable
     * par l'admin (historique conservé), mais ne peut plus se connecter.
     * C'est volontairement distinct de destroy() (soft delete) qui reste
     * disponible pour un retrait plus définitif.
     */
    public function toggleStatut(User $user)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => "Vous ne pouvez pas désactiver votre propre compte."]);
        }

        $user->update(['actif' => !$user->actif]);

        $action = $user->actif ? 'réactivé' : 'désactivé';

        ActivityLog::enregistrer(
            'utilisateur.' . ($user->actif ? 'reactive' : 'desactive'),
            "a {$action} le compte de {$user->nom_complet}",
            $user
        );

        return back()->with('success', "Le compte de {$user->nom_complet} a été {$action}.");
    }

    /**
     * Désactiver (soft delete) un compte — retrait définitif de la liste
     * active, mais les logs et l'audit restent consultables via user_id.
     */
    public function destroy(User $user)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($user);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => "Vous ne pouvez pas supprimer votre propre compte."]);
        }

        $nom = $user->nom_complet;

        ActivityLog::enregistrer('utilisateur.supprime', "a supprimé le compte de {$nom}", $user);

        $user->delete();

        return redirect()->route('parametres.utilisateurs.index')
            ->with('success', "Le compte de {$nom} a été supprimé.");
    }

    /**
     * Vérifie que l'utilisateur cible appartient à la même société
     */
    private function authorizeSameSociete(User $user): void
    {
        if ($user->societe_id !== auth()->user()->societe_id) {
            abort(403);
        }
    }
}
