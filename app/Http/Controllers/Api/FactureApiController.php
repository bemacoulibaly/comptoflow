<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\FactureResource;
use App\Models\Facture;
use App\Services\FactureService;
use Illuminate\Http\Request;
class FactureApiController extends Controller {
    public function __construct(private FactureService $service) {}
    public function index(Request $request) {
        $q = Facture::where('societe_id',$request->user()->societe_id)->with('tiers','lignes')
            ->when($request->type,     fn($q)=>$q->where('type',$request->type))
            ->when($request->statut,   fn($q)=>$q->where('statut',$request->statut))
            ->when($request->tiers_id, fn($q)=>$q->where('tiers_id',$request->tiers_id))
            ->when($request->debut,    fn($q)=>$q->where('date_emission','>=',$request->debut))
            ->when($request->fin,      fn($q)=>$q->where('date_emission','<=',$request->fin))
            ->orderByDesc('date_emission');
        return FactureResource::collection($q->paginate(25));
    }
    public function show(Request $request, Facture $facture) {
        abort_unless($facture->societe_id===$request->user()->societe_id,403);
        return new FactureResource($facture->load('tiers','lignes'));
    }
    public function store(Request $request) {
        abort_unless($request->user()->tokenCan('factures:write')&&$request->user()->peutEditer(),403);
        $data = $request->validate(['tiers_id'=>'required|integer','type'=>'required|in:client,fournisseur','date_emission'=>'required|date','date_echeance'=>'required|date|after_or_equal:date_emission','notes'=>'nullable|string','lignes'=>'required|array|min:1','lignes.*.designation'=>'required|string','lignes.*.quantite'=>'required|numeric|min:0.01','lignes.*.prix_unitaire'=>'required|numeric|min:0','lignes.*.taux_tva'=>'nullable|numeric|min:0']);
        $f = $this->service->creer($request->user()->societe,$data);
        return (new FactureResource($f->load('tiers','lignes')))->response()->setStatusCode(201);
    }
    public function paiement(Request $request, Facture $facture) {
        abort_unless($facture->societe_id===$request->user()->societe_id,403);
        $request->validate(['montant'=>'required|numeric|min:0.01','compte_reglement'=>'required|in:512,570']);
        return new FactureResource($this->service->enregistrerPaiement($facture,$request->montant,$request->compte_reglement)->load('tiers','lignes'));
    }
    public function enRetard(Request $request) {
        return FactureResource::collection(Facture::where('societe_id',$request->user()->societe_id)->enRetard()->with('tiers')->orderBy('date_echeance')->get());
    }
}
