<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class Rapprochement extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'societe_id','compte_id','date_debut','date_fin',
        'solde_releve','solde_comptable','ecart','statut','valide_at','valide_par',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'valide_at'  => 'datetime',
    ];

    public function societe()   { return $this->belongsTo(Societe::class); }
    public function compte()    { return $this->belongsTo(Compte::class); }
    public function lignes()    { return $this->hasMany(LigneRapprochement::class); }
    public function validePar() { return $this->belongsTo(User::class, 'valide_par'); }
}
