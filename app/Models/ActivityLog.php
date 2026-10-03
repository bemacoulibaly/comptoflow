<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'societe_id', 'user_id', 'user_nom_snapshot',
        'action', 'description', 'sujet_type', 'sujet_id', 'ip_address',
    ];

    public function societe() { return $this->belongsTo(Societe::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function sujet()   { return $this->morphTo(); }

    /**
     * Enregistre une entrée de log. user_nom_snapshot capture le nom
     * au moment de l'action, pour rester lisible même si le compte
     * est désactivé ou supprimé plus tard.
     */
    public static function enregistrer(string $action, string $description, ?\Illuminate\Database\Eloquent\Model $sujet = null): self
    {
        $user = auth()->user();

        return self::create([
            'societe_id'        => $user?->societe_id,
            'user_id'           => $user?->id,
            'user_nom_snapshot' => $user?->nom_complet,
            'action'            => $action,
            'description'       => $description,
            'sujet_type'        => $sujet ? get_class($sujet) : null,
            'sujet_id'          => $sujet?->id,
            'ip_address'        => request()?->ip(),
        ]);
    }
}
