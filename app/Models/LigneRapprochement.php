<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneRapprochement extends Model
{
    use HasFactory;

    protected $table = 'lignes_rapprochement';

    protected $fillable = [
        'rapprochement_id','ligne_ecriture_id',
        'date_operation','libelle','montant','source','pointe',
    ];

    protected $casts = [
        'date_operation' => 'date',
        'pointe'         => 'boolean',
    ];

    public function rapprochement() { return $this->belongsTo(Rapprochement::class); }
    public function ligneEcriture() { return $this->belongsTo(LigneEcriture::class); }
}
