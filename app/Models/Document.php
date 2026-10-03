<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'societe_id', 'user_id', 'documentable_type', 'documentable_id',
        'nom_original', 'chemin', 'type_mime', 'taille', 'categorie', 'description',
    ];

    public function documentable() { return $this->morphTo(); }
    public function societe()      { return $this->belongsTo(Societe::class); }
    public function user()         { return $this->belongsTo(User::class); }

    public function getTailleFormateeAttribute(): string
    {
        $kb = $this->taille / 1024;
        if ($kb < 1024) return round($kb, 1) . ' Ko';
        return round($kb / 1024, 2) . ' Mo';
    }

    public function getIconeAttribute(): string
    {
        return match (true) {
            str_contains($this->type_mime, 'pdf')   => 'ti-file-type-pdf',
            str_contains($this->type_mime, 'image') => 'ti-photo',
            str_contains($this->type_mime, 'excel') ||
            str_contains($this->type_mime, 'spreadsheet') => 'ti-file-spreadsheet',
            default => 'ti-file',
        };
    }

    public function getUrlAttribute(): string
    {
        return route('documents.download', $this);
    }

    public function supprimer(): void
    {
        Storage::disk('private')->delete($this->chemin);
        $this->delete();
    }

    /**
     * Types MIME autorisés — on n'accepte que des documents légitimes,
     * jamais d'exécutables ou de scripts.
     */
    public static function typesAcceptes(): array
    {
        return [
            'application/pdf',
            'image/jpeg', 'image/png', 'image/webp',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
        ];
    }
}
