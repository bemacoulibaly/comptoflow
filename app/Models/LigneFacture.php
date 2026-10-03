<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneFacture extends Model
{
    use HasFactory;

    protected $table = 'lignes_facture';

    protected $fillable = [
        'facture_id','designation','quantite','unite',
        'prix_unitaire','taux_tva','montant_ht','montant_tva','ordre',
    ];

    public function facture() { return $this->belongsTo(Facture::class); }

    protected static function booted(): void
    {
        static::saving(function (self $ligne) {
            $ligne->montant_ht  = round($ligne->quantite * $ligne->prix_unitaire, 2);
            $ligne->montant_tva = round($ligne->montant_ht * $ligne->taux_tva / 100, 2);
        });
    }
}
