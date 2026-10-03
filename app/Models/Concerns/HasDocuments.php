<?php

namespace App\Models\Concerns;

use App\Models\Document;

trait HasDocuments
{
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }

    public function ajouterDocument(\Illuminate\Http\UploadedFile $fichier, string $categorie = 'autre', ?string $description = null): Document
    {
        $societe = auth()->user()->societe;
        $chemin  = $fichier->store("societes/{$societe->id}/documents", 'private');

        return $this->documents()->create([
            'societe_id'   => $societe->id,
            'user_id'      => auth()->id(),
            'nom_original' => $fichier->getClientOriginalName(),
            'chemin'       => $chemin,
            'type_mime'    => $fichier->getMimeType(),
            'taille'       => $fichier->getSize(),
            'categorie'    => $categorie,
            'description'  => $description,
        ]);
    }
}
