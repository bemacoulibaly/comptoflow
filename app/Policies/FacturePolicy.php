<?php
namespace App\Policies;
use App\Models\Facture;
use App\Models\User;

class FacturePolicy
{
    public function viewAny(User $user): bool { return true; }

    public function view(User $user, Facture $facture): bool
    {
        return $user->societe_id === $facture->societe_id;
    }

    public function create(User $user): bool { return $user->peutEditer(); }

    public function update(User $user, Facture $facture): bool
    {
        return $user->peutEditer()
            && $user->societe_id === $facture->societe_id
            && !in_array($facture->statut, ['annulee', 'payee']);
    }

    public function delete(User $user, Facture $facture): bool
    {
        return $user->isAdmin()
            && $user->societe_id === $facture->societe_id
            && $facture->statut === 'brouillon';
    }
}
