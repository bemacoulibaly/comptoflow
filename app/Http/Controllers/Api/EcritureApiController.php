<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\EcritureResource;
use App\Models\Ecriture;
use App\Services\EcritureService;
use Illuminate\Http\Request;
class EcritureApiController extends Controller {
    public function __construct(private EcritureService $service) {}
    public function index(Request $request) {
        $q = Ecriture::where('societe_id',$request->user()->societe_id)
            ->with('lignes.compte')
            ->when($request->statut,  fn($q)=>$q->where('statut',$request->statut))
            ->when($request->journal, fn($q)=>$q->where('journal',$request->journal))
            ->when($request->debut,   fn($q)=>$q->where('date_ecriture','>=',$request->debut))
            ->when($request->fin,     fn($q)=>$q->where('date_ecriture','<=',$request->fin))
            ->when($request->search,  fn($q)=>$q->where('libelle','like',"%{$request->search}%"))
            ->orderByDesc('date_ecriture')->orderByDesc('id');
        return EcritureResource::collection($q->paginate(25));
    }
    public function show(Request $request, Ecriture $ecriture) {
        abort_unless($ecriture->societe_id===$request->user()->societe_id,403);
        return new EcritureResource($ecriture->load('lignes.compte'));
    }
    public function store(Request $request) {
        abort_unless($request->user()->tokenCan('ecritures:write')&&$request->user()->peutEditer(),403);
        $data = $request->validate(['date_ecriture'=>'required|date','journal'=>'required|in:BQ,CA,AC,VT,OD,SA','libelle'=>'required|string|max:255','lignes'=>'required|array|min:2','lignes.*.numero_compte'=>'required|string','lignes.*.libelle'=>'required|string','lignes.*.debit'=>'required|numeric|min:0','lignes.*.credit'=>'required|numeric|min:0']);
        $e = $this->service->creer($request->user()->societe,$data);
        return (new EcritureResource($e->load('lignes.compte')))->response()->setStatusCode(201);
    }
    public function valider(Request $request, Ecriture $ecriture) {
        abort_unless($ecriture->societe_id===$request->user()->societe_id&&$request->user()->peutEditer(),403);
        try { $ecriture->valider(); return new EcritureResource($ecriture->fresh()->load('lignes.compte')); }
        catch (\DomainException $e) { return response()->json(['message'=>$e->getMessage()],422); }
    }
}
