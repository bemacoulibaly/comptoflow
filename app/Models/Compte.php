<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    use HasFactory;

    protected $fillable = ['societe_id', 'numero', 'libelle', 'classe', 'type', 'actif'];

    public function societe()       { return $this->belongsTo(Societe::class); }
    public function lignes()        { return $this->hasMany(LigneEcriture::class); }

    public function getSoldeAttribute(): float
    {
        $debit  = $this->lignes()->sum('debit');
        $credit = $this->lignes()->sum('credit');
        return in_array($this->type, ['actif', 'charge'])
            ? $debit - $credit
            : $credit - $debit;
    }

    public function scopeActif($q)  { return $q->where('actif', true); }
    public function scopeClasse($q, string $c) { return $q->where('classe', $c); }
}
