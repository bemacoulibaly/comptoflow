<?php
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
Broadcast::channel('societe.{societeId}', function (User $user, int $societeId) {
    return (int) $user->societe_id === $societeId;
});
Broadcast::channel('presence.societe.{societeId}', function (User $user, int $societeId) {
    if ((int) $user->societe_id !== $societeId) return false;
    return ['id'=>$user->id,'nom'=>$user->nom_complet,'initiales'=>$user->initiales];
});
