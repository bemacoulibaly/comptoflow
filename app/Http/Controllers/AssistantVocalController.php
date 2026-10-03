<?php
namespace App\Http\Controllers;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class AssistantVocalController extends Controller {
    public function __construct(private DashboardService $ds) {}
    public function repondre(Request $request) {
        $request->validate(['question'=>'required|string|max:500']);
        $societe = auth()->user()->societe;
        $stats   = $this->ds->getStats($societe,'mois');
        $fmt     = fn($n) => number_format($n,0,',',' ').' FCFA';
        $ctx = implode("\n",["- Société: {$societe->raison_sociale}","- Trésorerie: {$fmt($stats['tresorerie']??0)}","- Recettes HT: {$fmt($stats['recettes']??0)}","- Charges HT: {$fmt($stats['charges']??0)}","- Résultat: {$fmt($stats['resultat']??0)}","- TVA nette: {$fmt($stats['tva']??0)}","- Factures retard: ".($stats['enRetard']??0),"- Écritures à valider: ".($stats['aValider']??0)]);
        $prompt = "Tu es l'assistant comptable de {$societe->raison_sociale} (SYSCOHADA, Côte d'Ivoire). Réponds en 2-3 phrases max, en français, basé sur ces données:\n{$ctx}\n\nQUESTION: {$request->question}";
        try {
            $res = Http::withHeaders(['x-api-key'=>config('services.anthropic.key'),'anthropic-version'=>'2023-06-01'])->timeout(20)->post('https://api.anthropic.com/v1/messages',['model'=>'claude-haiku-4-5-20251001','max_tokens'=>300,'messages'=>[['role'=>'user','content'=>$prompt]]]);
            return response()->json(['reponse'=>$res->successful()?trim($res->json('content.0.text','')):'Service indisponible.','question'=>$request->question,'erreur'=>!$res->successful()]);
        } catch (\Exception $e) { return response()->json(['reponse'=>'Erreur survenue.','erreur'=>true]); }
    }
}
