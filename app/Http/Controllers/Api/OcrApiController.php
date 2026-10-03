<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Tiers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class OcrApiController extends Controller {
    public function extraire(Request $request) {
        $request->validate(['image'=>'required|string','media_type'=>'required|in:image/jpeg,image/png,image/webp']);
        $societe = $request->user()->societe;
        $fourn   = Tiers::where('societe_id',$societe->id)->where('actif',true)->whereIn('type',['fournisseur','les_deux'])->pluck('nom')->take(30)->implode(', ');
        $prompt  = "Analyse cette facture. Fournisseurs connus: {$fourn}. Réponds UNIQUEMENT en JSON: {\"fournisseur\":\"...\",\"fournisseur_connu\":\"...ou null\",\"date\":\"YYYY-MM-DD\",\"numero_facture\":\"...\",\"montant_ht\":0.00,\"taux_tva\":18,\"montant_tva\":0.00,\"montant_ttc\":0.00,\"devise\":\"XOF\",\"description\":\"...\",\"confiance\":\"haute|moyenne|faible\"}";
        try {
            $res = Http::withHeaders(['x-api-key'=>config('services.anthropic.key'),'anthropic-version'=>'2023-06-01'])->timeout(30)->post('https://api.anthropic.com/v1/messages',['model'=>'claude-opus-4-6','max_tokens'=>500,'messages'=>[['role'=>'user','content'=>[['type'=>'image','source'=>['type'=>'base64','media_type'=>$request->media_type,'data'=>$request->image]],['type'=>'text','text'=>$prompt]]]]]);
            if (!$res->successful()) return response()->json(['message'=>'Service OCR indisponible.'],503);
            $data = json_decode(trim(preg_replace('/```json\s*|\s*```/','',$res->json('content.0.text',''))),true);
            if (!$data) return response()->json(['message'=>'Extraction impossible.'],422);
            $tiersId = $data['fournisseur_connu'] ? Tiers::where('societe_id',$societe->id)->where('nom',$data['fournisseur_connu'])->value('id') : null;
            return response()->json(['extraction'=>$data,'tiers_id'=>$tiersId,'prefill'=>['tiers_id'=>$tiersId,'type'=>'fournisseur','date_emission'=>$data['date']??null,'notes'=>$data['description']??null,'lignes'=>$data['description']?[['designation'=>$data['description'],'quantite'=>1,'prix_unitaire'=>$data['montant_ht']??0,'taux_tva'=>$data['taux_tva']??18]]:[]]]);
        } catch (\Exception $e) { return response()->json(['message'=>'Erreur analyse.'],503); }
    }
}
