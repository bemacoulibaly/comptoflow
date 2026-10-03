<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasDocuments;

class Ecriture extends Model
{
    use HasFactory, SoftDeletes, Auditable, HasDocuments;

    protected $fillable = [
        'societe_id','user_id','numero_piece','date_ecriture',
        'journal','libelle','reference_tiers','statut','validee_at',
    ];

    protected $casts = [
        'date_ecriture' => 'date',
        'validee_at'    => 'datetime',
    ];

    public function societe() { return $this->belongsTo(Societe::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function lignes()  { return $this->hasMany(LigneEcriture::class)->orderBy('ordre'); }

    public function getTotalDebitAttribute(): float
    {
        return $this->lignes->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return $this->lignes->sum('credit');
    }

    public function isEquilibree(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function valider(): void
    {
        if (!$this->isEquilibree()) {
            throw new \DomainException("L'écriture n'est pas équilibrée (débit ≠ crédit).");
        }
        $this->update(['statut' => 'validee', 'validee_at' => now()]);
        broadcast(new \App\Events\EcritureValidee($this->fresh()))->toOthers();
    }

    public function scopeValidees($q)        { return $q->where('statut', 'validee'); }
    public function scopeJournal($q, $j)     { return $q->where('journal', $j); }
    public function scopePeriode($q, $d, $f) { return $q->whereBetween('date_ecriture', [$d, $f]); }
}
