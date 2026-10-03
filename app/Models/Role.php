<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['societe_id', 'nom', 'slug', 'couleur', 'est_systeme'];

    protected $casts = ['est_systeme' => 'boolean'];

    /**
     * Pages disponibles dans l'application — référence unique utilisée
     * pour la gestion des permissions et le middleware de verrouillage.
     */
    public const PAGES = [
        'dashboard'     => 'Tableau de bord',
        'ecritures'     => 'Saisie / Écritures',
        'factures'      => 'Factures',
        'tiers'         => 'Tiers',
        'rapprochement' => 'Rapprochement bancaire',
        'bilan'         => 'Bilan & Résultat',
        'tva'           => 'TVA',
        'calendrier'    => 'Calendrier',
        'parametres'    => 'Paramètres',
    ];

    public function societe()    { return $this->belongsTo(Societe::class); }
    public function users()      { return $this->hasMany(User::class); }
    public function permissions(){ return $this->hasMany(RolePermission::class); }

    /**
     * true si ce rôle a accès à la page donnée.
     * Par défaut (aucune ligne en base pour cette page) = autorisé,
     * pour ne pas casser un rôle existant si une nouvelle page est ajoutée plus tard.
     */
    public function peutAccederA(string $page): bool
    {
        $perm = $this->permissions->firstWhere('page', $page);
        return $perm ? $perm->autorise : true;
    }

    public static function genererSlug(string $nom, int $societeId): string
    {
        $base = Str::slug($nom);
        $slug = $base;
        $i = 1;
        while (self::where('societe_id', $societeId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}
