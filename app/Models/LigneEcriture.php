<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneEcriture extends Model
{
    use HasFactory;

    protected $table = 'lignes_ecriture';

    protected $fillable = [
        'ecriture_id','compte_id','tiers_id','libelle','debit','credit','ordre',
    ];

    public function ecriture() { return $this->belongsTo(Ecriture::class); }
    public function compte()   { return $this->belongsTo(Compte::class); }
    public function tiers()    { return $this->belongsTo(Tiers::class); }
}
