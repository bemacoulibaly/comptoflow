<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IaSuggestionController extends Controller
{
    /**
     * Suggère un compte comptable SYSCOHADA via l'API Claude.
     * Appelé en AJAX depuis le formulaire de saisie d'écriture.
     *
     * Entrée  : libelle (string)
     * Sortie  : { numero, libelle, explication }
     */
    public function suggerer(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
        ]);

        $societe = auth()->user()->societe;
        $libelle = $request->libelle;

        // Récupérer les comptes de la société pour le contexte
        $comptes = Compte::where('societe_id', $societe->id)
            ->where('actif', true)
            ->orderBy('numero')
            ->get(['numero', 'libelle', 'type'])
            ->map(fn($c) => "{$c->numero} — {$c->libelle} ({$c->type})")
            ->take(80) // Limiter pour rester dans les tokens
            ->implode("\n");

        $prompt = <<<PROMPT
Tu es un expert-comptable SYSCOHADA en Côte d'Ivoire.
Voici le plan comptable de cette société :

{$comptes}

L'utilisateur saisit l'écriture suivante : "{$libelle}"

Quel compte comptable SYSCOHADA est le plus approprié pour cette écriture ?
Réponds UNIQUEMENT en JSON valide, sans aucun texte avant ou après, dans ce format exact :
{"numero":"xxx","libelle":"libellé du compte","explication":"explication courte en 1 phrase"}
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001', // modèle rapide pour la suggestion temps réel
                'max_tokens' => 200,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (!$response->successful()) {
                return response()->json(['erreur' => 'Service IA indisponible.'], 503);
            }

            $texte = $response->json('content.0.text', '');
            // Nettoyer les éventuels blocs markdown ```json
            $texte = preg_replace('/^```json?\s*/m', '', $texte);
            $texte = preg_replace('/^```\s*/m', '', $texte);
            $data  = json_decode(trim($texte), true);

            if (!$data || !isset($data['numero'])) {
                return response()->json(['erreur' => 'Réponse IA non exploitable.'], 422);
            }

            // Vérifier que le compte suggéré existe bien dans le plan de la société
            $compteExiste = Compte::where('societe_id', $societe->id)
                ->where('numero', $data['numero'])
                ->exists();

            return response()->json([
                'numero'       => $data['numero'],
                'libelle'      => $data['libelle'],
                'explication'  => $data['explication'],
                'dans_plan'    => $compteExiste,
            ]);

        } catch (\Exception $e) {
            return response()->json(['erreur' => 'Erreur de connexion au service IA.'], 503);
        }
    }
}
