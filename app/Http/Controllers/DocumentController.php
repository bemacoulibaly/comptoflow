<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Ecriture;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Upload d'un justificatif attaché à une écriture ou une facture.
     * Le type du parent est passé via ?type=ecriture ou ?type=facture
     */
    public function store(Request $request)
    {
        $request->validate([
            'fichier'        => [
                'required', 'file',
                'max:10240', // 10 Mo max
                'mimes:pdf,jpg,jpeg,png,webp,xls,xlsx,csv',
            ],
            'parent_type'    => 'required|in:ecriture,facture',
            'parent_id'      => 'required|integer',
            'categorie'      => 'required|in:facture,devis,contrat,recu,bon_livraison,releve,autre',
            'description'    => 'nullable|string|max:255',
        ]);

        $parent = match ($request->parent_type) {
            'ecriture' => Ecriture::where('societe_id', auth()->user()->societe_id)->findOrFail($request->parent_id),
            'facture'  => Facture::where('societe_id', auth()->user()->societe_id)->findOrFail($request->parent_id),
        };

        $this->authorize('update', $parent);

        $doc = $parent->ajouterDocument(
            $request->file('fichier'),
            $request->categorie,
            $request->description
        );

        if ($request->wantsJson()) {
            return response()->json([
                'id'         => $doc->id,
                'nom'        => $doc->nom_original,
                'taille'     => $doc->taille_formatee,
                'icone'      => $doc->icone,
                'url'        => $doc->url,
                'categorie'  => $doc->categorie,
            ]);
        }

        return back()->with('success', "Justificatif « {$doc->nom_original} » joint.");
    }

    /**
     * Téléchargement sécurisé — vérifie l'appartenance à la société
     * avant de renvoyer le fichier depuis le disque privé.
     */
    public function download(Document $document)
    {
        abort_unless($document->societe_id === auth()->user()->societe_id, 403);

        if (!Storage::disk('private')->exists($document->chemin)) {
            abort(404, 'Fichier introuvable sur le serveur.');
        }

        return Storage::disk('private')->download(
            $document->chemin,
            $document->nom_original
        );
    }

    /**
     * Suppression d'un justificatif (supprime aussi le fichier physique).
     */
    public function destroy(Document $document)
    {
        abort_unless($document->societe_id === auth()->user()->societe_id, 403);
        abort_unless(auth()->user()->peutEditer() || $document->user_id === auth()->id(), 403);

        $nom = $document->nom_original;
        $document->supprimer();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', "Justificatif « {$nom} » supprimé.");
    }
}
