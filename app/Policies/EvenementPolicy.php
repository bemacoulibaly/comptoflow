<?php

namespace App\Policies;

use App\Models\Evenement;
use App\Models\User;

class EvenementPolicy
{
    public function view(User $user, Evenement $evenement): bool
    {
        return $user->societe_id === $evenement->societe_id;
    }

    public function create(User $user): bool
    {
        return $user->peutEditer();
    }

    public function delete(User $user, Evenement $evenement): bool
    {
        return $user->societe_id === $evenement->societe_id
            && ($user->isAdmin() || $evenement->user_id === $user->id);
    }
}
