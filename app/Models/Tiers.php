<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Auditable;

class Tiers extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'societe_id', 'type', 'nom', 'email', 'telephone',
        'adresse', 'ville', 'numero_contribuable', 'actif',
    ];

    public function societe()  { return $this->belongsTo(Societe::class); }
    public function factures() { return $this->hasMany(Facture::class); }

    public function getInitialesAttribute(): string
    {
        $mots = explode(' ', $this->nom);
        return strtoupper(implode('', array_map(fn($m) => substr($m, 0, 1), array_slice($mots, 0, 2))));
    }

    public function getSoldeClientAttribute(): float
    {
        return $this->factures()->where('type', 'client')
            ->whereIn('statut', ['emise', 'partielle', 'en_retard'])
            ->sum('montant_ttc') - $this->factures()->where('type', 'client')->sum('montant_paye');
    }
}
