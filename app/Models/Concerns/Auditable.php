<?php

namespace App\Models\Concerns;

use App\Models\AuditTrail;

/**
 * À ajouter sur les modèles métier (Ecriture, Facture, Tiers, etc.)
 * pour que chaque création / modification / suppression soit tracée
 * automatiquement dans audit_trails, sans avoir à y penser dans
 * chaque contrôleur.
 *
 * Champs exclus par défaut (bruit, jamais utile en historique) :
 * timestamps et clé primaire. Surcharger $auditExclude sur le modèle
 * pour exclure d'autres champs (ex: mot de passe).
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditTrail::enregistrer('cree', $model, null, $model->auditableAttributes());
        });

        static::updated(function ($model) {
            $avant = array_intersect_key_safe($model->getOriginal(), $model->auditableAttributes());
            $apres = $model->auditableAttributes();
            // Ne logguer que s'il y a un changement réel sur des champs suivis
            if ($avant != $apres) {
                AuditTrail::enregistrer('modifie', $model, $avant, $apres);
            }
        });

        static::deleted(function ($model) {
            AuditTrail::enregistrer('supprime', $model, $model->auditableAttributes(), null);
        });
    }

    public function auditableAttributes(): array
    {
        $exclude = array_merge(['created_at', 'updated_at', 'deleted_at', 'id'], $this->auditExclude ?? []);
        return array_diff_key($this->getAttributes(), array_flip($exclude));
    }

    public function auditTrails()
    {
        return $this->morphMany(AuditTrail::class, 'sujet');
    }
}

if (!function_exists('array_intersect_key_safe')) {
    function array_intersect_key_safe(array $array, array $keysReference): array
    {
        return array_intersect_key($array, $keysReference);
    }
}
