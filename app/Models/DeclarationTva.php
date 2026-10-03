<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class DeclarationTva extends Model
{
    use HasFactory, Auditable;

    protected $table = 'declarations_tva';

    protected $fillable = [
        'societe_id','periode_debut','periode_fin',
        'tva_collectee','tva_deductible','tva_nette',
        'statut','date_echeance','validee_at',
    ];

    protected $casts = [
        'periode_debut'  => 'date',
        'periode_fin'    => 'date',
        'date_echeance'  => 'date',
        'validee_at'     => 'datetime',
    ];

    public function societe() { return $this->belongsTo(Societe::class); }
}
