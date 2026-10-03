<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasDocuments;

class Facture extends Model
{
    use HasFactory, SoftDeletes, Auditable, HasDocuments;

    protected $fillable = [
        'societe_id','tiers_id','user_id','numero','type',
        'date_emission','date_echeance','montant_ht','taux_tva',
        'montant_tva','montant_ttc','montant_paye','statut','notes','ecriture_id',
    ];

    protected $casts = [
        'date_emission'  => 'date',
        'date_echeance'  => 'date',
        'montant_ht'     => 'float',
        'montant_tva'    => 'float',
        'montant_ttc'    => 'float',
        'montant_paye'   => 'float',
    ];

    public function societe()  { return $this->belongsTo(Societe::class); }
    public function tiers()    { return $this->belongsTo(Tiers::class); }
    public function user()     { return $this->belongsTo(User::class); }
    public function lignes()   { return $this->hasMany(LigneFacture::class)->orderBy('ordre'); }
    public function ecriture() { return $this->belongsTo(Ecriture::class); }

    public function getSoldeAttribute(): float { return $this->montant_ttc - $this->montant_paye; }
    public function isEnRetard(): bool { return $this->statut !== 'payee' && $this->date_echeance->isPast(); }

    public function recalculerTotaux(): void
    {
        $ht  = $this->lignes->sum('montant_ht');
        $tva = $this->lignes->sum('montant_tva');
        $this->update(['montant_ht' => $ht, 'montant_tva' => $tva, 'montant_ttc' => $ht + $tva]);
    }

    public function scopeEnRetard($q) { return $q->whereNotIn('statut',['payee','annulee'])->where('date_echeance','<',now()); }
    public function scopeClients($q)       { return $q->where('type','client'); }
    public function scopeFournisseurs($q)  { return $q->where('type','fournisseur'); }
}
