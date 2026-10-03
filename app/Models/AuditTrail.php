<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'societe_id', 'user_id', 'user_nom_snapshot',
        'sujet_type', 'sujet_id', 'evenement', 'valeurs_avant', 'valeurs_apres',
    ];

    protected $casts = [
        'valeurs_avant' => 'array',
        'valeurs_apres' => 'array',
    ];

    public function societe() { return $this->belongsTo(Societe::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function sujet()   { return $this->morphTo(); }

    /**
     * Calcule la liste des champs qui ont changé entre avant/après,
     * pour affichage façon "diff" dans la vue admin.
     */
    public function getChangementsAttribute(): array
    {
        if ($this->evenement !== 'modifie' || !$this->valeurs_avant || !$this->valeurs_apres) {
            return [];
        }

        $changements = [];
        foreach ($this->valeurs_apres as $champ => $nouvelle) {
            $ancienne = $this->valeurs_avant[$champ] ?? null;
            if ($ancienne !== $nouvelle) {
                $changements[$champ] = ['avant' => $ancienne, 'apres' => $nouvelle];
            }
        }
        return $changements;
    }

    public static function enregistrer(string $evenement, \Illuminate\Database\Eloquent\Model $sujet, ?array $avant = null, ?array $apres = null): self
    {
        $user = auth()->user();

        return self::create([
            'societe_id'        => $user?->societe_id,
            'user_id'           => $user?->id,
            'user_nom_snapshot' => $user?->nom_complet,
            'sujet_type'        => get_class($sujet),
            'sujet_id'          => $sujet->id,
            'evenement'         => $evenement,
            'valeurs_avant'     => $avant,
            'valeurs_apres'     => $apres,
        ]);
    }
}
