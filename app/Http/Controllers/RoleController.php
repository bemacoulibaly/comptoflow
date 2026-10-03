<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Même pattern que UserManagementController : pas de HasMiddleware
     * (conflit de signature avec Illuminate\Routing\Controller sur
     * certaines versions de Laravel 11), vérification explicite à
     * chaque méthode.
     */
    private function ensureIsAdmin(): void
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, "Seul un administrateur peut gérer les rôles.");
        }
    }

    public function index()
    {
        $this->ensureIsAdmin();

        $societe = auth()->user()->societe;
        $roles = Role::where('societe_id', $societe->id)
            ->withCount('users')
            ->orderByDesc('est_systeme')
            ->orderBy('nom')
            ->get();

        return view('parametres.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->ensureIsAdmin();

        return view('parametres.roles.create', ['pages' => Role::PAGES]);
    }

    public function store(Request $request)
    {
        $this->ensureIsAdmin();

        $societe = auth()->user()->societe;

        $data = $request->validate([
            'nom'     => ['required', 'string', 'max:100'],
            'couleur' => ['required', 'in:green,blue,amber,red,gray,purple'],
            'pages'   => ['array'],
            'pages.*' => ['string', 'in:' . implode(',', array_keys(Role::PAGES))],
        ]);

        $role = Role::create([
            'societe_id'  => $societe->id,
            'nom'         => $data['nom'],
            'slug'        => Role::genererSlug($data['nom'], $societe->id),
            'couleur'     => $data['couleur'],
            'est_systeme' => false,
        ]);

        $this->synchroniserPermissions($role, $data['pages'] ?? []);

        ActivityLog::enregistrer('role.cree', "a créé le rôle « {$role->nom} »", $role);

        return redirect()->route('parametres.roles.index')
            ->with('success', "Le rôle « {$role->nom} » a été créé.");
    }

    public function edit(Role $role)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($role);

        if ($role->est_systeme) {
            abort(403, "Les rôles système (Administrateur, Éditeur, Lecteur) ne peuvent pas être modifiés.");
        }

        $permissionsActuelles = $role->permissions->pluck('autorise', 'page');

        return view('parametres.roles.edit', [
            'role'  => $role,
            'pages' => Role::PAGES,
            'permissionsActuelles' => $permissionsActuelles,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($role);

        if ($role->est_systeme) {
            abort(403, "Les rôles système ne peuvent pas être modifiés.");
        }

        $data = $request->validate([
            'nom'     => ['required', 'string', 'max:100'],
            'couleur' => ['required', 'in:green,blue,amber,red,gray,purple'],
            'pages'   => ['array'],
            'pages.*' => ['string', 'in:' . implode(',', array_keys(Role::PAGES))],
        ]);

        $role->update([
            'nom'     => $data['nom'],
            'couleur' => $data['couleur'],
        ]);

        $this->synchroniserPermissions($role, $data['pages'] ?? []);

        ActivityLog::enregistrer('role.modifie', "a modifié le rôle « {$role->nom} »", $role);

        return redirect()->route('parametres.roles.index')
            ->with('success', "Le rôle « {$role->nom} » a été mis à jour.");
    }

    public function destroy(Role $role)
    {
        $this->ensureIsAdmin();
        $this->authorizeSameSociete($role);

        if ($role->est_systeme) {
            abort(403, "Les rôles système ne peuvent pas être supprimés.");
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors([
                'role' => "Impossible de supprimer ce rôle : {$role->users()->count()} compte(s) y sont encore assignés. Réassignez-les d'abord.",
            ]);
        }

        $nom = $role->nom;
        $role->delete();

        ActivityLog::enregistrer('role.supprime', "a supprimé le rôle « {$nom} »");

        return redirect()->route('parametres.roles.index')
            ->with('success', "Le rôle « {$nom} » a été supprimé.");
    }

    /**
     * Crée/met à jour une ligne de permission pour CHAQUE page connue,
     * autorise=true si cochée dans le formulaire, false sinon.
     * Explicite et non ambigu : aucune page n'est laissée "implicite".
     */
    private function synchroniserPermissions(Role $role, array $pagesAutorisees): void
    {
        foreach (array_keys(Role::PAGES) as $page) {
            RolePermission::updateOrCreate(
                ['role_id' => $role->id, 'page' => $page],
                ['autorise' => in_array($page, $pagesAutorisees)]
            );
        }
    }

    private function authorizeSameSociete(Role $role): void
    {
        if ($role->societe_id !== auth()->user()->societe_id) {
            abort(403);
        }
    }
}
