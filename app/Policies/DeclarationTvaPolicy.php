<?php
namespace App\Policies;
use App\Models\DeclarationTva;
use App\Models\User;

class DeclarationTvaPolicy
{
    public function view(User $user, DeclarationTva $declaration): bool
    {
        return $user->societe_id === $declaration->societe_id;
    }

    public function update(User $user, DeclarationTva $declaration): bool
    {
        return $user->peutEditer()
            && $user->societe_id === $declaration->societe_id
            && $declaration->statut === 'brouillon';
    }
}
