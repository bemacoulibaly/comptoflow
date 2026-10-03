<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = [
        'societe_id','user_id','titre','description',
        'date_echeance','type','priorite','notifie',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'notifie'       => 'boolean',
    ];

    public function societe() { return $this->belongsTo(Societe::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public function scopeAVenir($q)  { return $q->where('date_echeance', '>=', now()->toDateString()); }
    public function scopeUrgents($q) { return $q->where('priorite', 'haute'); }
}
