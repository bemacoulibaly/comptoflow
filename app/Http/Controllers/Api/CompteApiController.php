<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompteResource;
use App\Models\Compte;
use Illuminate\Http\Request;
class CompteApiController extends Controller {
    public function index(Request $request) {
        $q = Compte::where('societe_id',$request->user()->societe_id)
            ->when($request->classe, fn($q)=>$q->where('classe',$request->classe))
            ->when($request->type,   fn($q)=>$q->where('type',$request->type))
            ->when($request->search, fn($q)=>$q->where(fn($q2)=>$q2->where('numero','like',"%{$request->search}%")->orWhere('libelle','like',"%{$request->search}%")))
            ->where('actif',true)->orderBy('numero');
        return CompteResource::collection($q->get());
    }
}
