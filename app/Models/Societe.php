<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Societe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'raison_sociale', 'numero_contribuable', 'regime_fiscal',
        'devise', 'taux_tva', 'adresse', 'ville', 'telephone', 'email', 'logo',
    ];

    public function users()      { return $this->hasMany(User::class); }
    public function comptes()    { return $this->hasMany(Compte::class); }
    public function tiers()      { return $this->hasMany(Tiers::class); }
    public function ecritures()  { return $this->hasMany(Ecriture::class); }
    public function factures()   { return $this->hasMany(Facture::class); }
    public function declarationsTva() { return $this->hasMany(DeclarationTva::class); }
    public function rapprochements()  { return $this->hasMany(Rapprochement::class); }
    public function evenements()      { return $this->hasMany(Evenement::class); }
}
