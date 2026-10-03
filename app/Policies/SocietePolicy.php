<?php
namespace App\Policies;
use App\Models\Societe;
use App\Models\User;

class SocietePolicy
{
    public function update(User $user, Societe $societe): bool
    {
        return $user->isAdmin() && $user->societe_id === $societe->id;
    }
}
