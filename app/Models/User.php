<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = ['nom', 'prenom', 'email', 'password', 'role', 'role_id', 'societe_id', 'actif'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'actif'             => 'boolean',
    ];

    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }

    public function roleAssigne()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function ecritures()
    {
        return $this->hasMany(Ecriture::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * isAdmin() reste basé sur la colonne 'role' (et non role_id) :
     * l'administrateur est un statut système protégé, jamais un rôle
     * personnalisé que l'admin pourrait renommer ou supprimer par erreur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * peutEditer() reste un raccourci legacy basé sur la colonne role,
     * utilisé par les Policies existantes (EcriturePolicy, FacturePolicy...).
     * Pour les rôles personnalisés assignés via role_id, c'est la permission
     * de page (peutAccederA) qui prévaut pour l'accès en lecture/écriture
     * à une section donnée.
     */
    public function peutEditer(): bool
    {
        if (in_array($this->role, ['admin', 'editeur'])) {
            return true;
        }
        // Un rôle personnalisé (role_id) sans le flag legacy 'editeur'/'admin'
        // est traité comme lecture seule par défaut, sauf si le rôle système
        // sous-jacent est 'editeur'.
        return false;
    }

    /**
     * Vérifie si ce compte a accès à une page donnée.
     * L'admin a TOUJOURS accès à tout, sans exception — c'est la garantie
     * centrale demandée : aucune page ne peut être verrouillée pour un admin.
     */
    public function peutAccederA(string $page): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->roleAssigne) {
            return $this->roleAssigne->peutAccederA($page);
        }

        // Pas de rôle personnalisé assigné : fallback sur l'ancien système
        // (éditeur/lecteur ont accès à tout sauf paramètres, comportement historique)
        if ($page === 'parametres') {
            return false;
        }
        return true;
    }

    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function getInitialesAttribute(): string
    {
        return strtoupper(substr($this->prenom, 0, 1) . substr($this->nom, 0, 1));
    }

    public function getNomRoleAffichageAttribute(): string
    {
        if ($this->roleAssigne) {
            return $this->roleAssigne->nom;
        }
        return match($this->role) {
            'admin'   => 'Administrateur',
            'editeur' => 'Éditeur',
            'lecteur' => 'Lecteur',
            default   => ucfirst($this->role),
        };
    }
}

