<?php
namespace App\Policies;
use App\Models\Rapprochement;
use App\Models\User;

class RapprochementPolicy
{
    public function view(User $user, Rapprochement $r): bool
    {
        return $user->societe_id === $r->societe_id;
    }

    public function update(User $user, Rapprochement $r): bool
    {
        return $user->peutEditer()
            && $user->societe_id === $r->societe_id
            && $r->statut === 'en_cours';
    }
}
