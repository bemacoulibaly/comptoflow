<?php
namespace App\Policies;
use App\Models\Ecriture;
use App\Models\User;

class EcriturePolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Ecriture $ecriture): bool
    {
        return $user->societe_id === $ecriture->societe_id;
    }

    public function create(User $user): bool { return $user->peutEditer(); }

    public function update(User $user, Ecriture $ecriture): bool
    {
        return $user->peutEditer()
            && $user->societe_id === $ecriture->societe_id
            && $ecriture->statut === 'brouillon';
    }

    public function delete(User $user, Ecriture $ecriture): bool
    {
        return $user->isAdmin()
            && $user->societe_id === $ecriture->societe_id
            && $ecriture->statut === 'brouillon';
    }
}
