<?php
namespace App\Policies;
use App\Models\Tiers;
use App\Models\User;

class TiersPolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Tiers $tiers): bool
    {
        return $user->societe_id === $tiers->societe_id;
    }

    public function create(User $user): bool { return $user->peutEditer(); }

    public function update(User $user, Tiers $tiers): bool
    {
        return $user->peutEditer() && $user->societe_id === $tiers->societe_id;
    }

    public function delete(User $user, Tiers $tiers): bool
    {
        return $user->isAdmin() && $user->societe_id === $tiers->societe_id;
    }
}
