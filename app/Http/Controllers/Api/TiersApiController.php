<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompteResource;
use App\Http\Resources\TiersResource;
use App\Models\Compte;
use App\Models\Tiers;
use Illuminate\Http\Request;
class TiersApiController extends Controller {
    public function index(Request $request) {
        $q = Tiers::where('societe_id',$request->user()->societe_id)
            ->when($request->type,   fn($q)=>$q->where('type',$request->type))
            ->when($request->search, fn($q)=>$q->where('nom','like',"%{$request->search}%"))
            ->when(!$request->boolean('inactifs'), fn($q)=>$q->where('actif',true))
            ->orderBy('nom');
        return TiersResource::collection($q->paginate(50));
    }
    public function show(Request $request, Tiers $tiers) {
        abort_unless($tiers->societe_id===$request->user()->societe_id,403);
        return new TiersResource($tiers);
    }
    public function store(Request $request) {
        abort_unless($request->user()->tokenCan('tiers:write')&&$request->user()->peutEditer(),403);
        $data = $request->validate(['type'=>'required|in:client,fournisseur,les_deux','nom'=>'required|string|max:191','email'=>'nullable|email','telephone'=>'nullable|string|max:30','adresse'=>'nullable|string','ville'=>'nullable|string','numero_contribuable'=>'nullable|string']);
        return (new TiersResource(Tiers::create(array_merge($data,['societe_id'=>$request->user()->societe_id]))))->response()->setStatusCode(201);
    }
}
