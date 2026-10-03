<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class AuthApiController extends Controller {
    public function login(Request $request) {
        $request->validate(['email'=>'required|email','password'=>'required','device_name'=>'required|string|max:100']);
        if (!Auth::attempt($request->only('email','password')))
            throw ValidationException::withMessages(['email'=>'Identifiants incorrects.']);
        $user = Auth::user();
        if (!$user->actif) { Auth::logout(); throw ValidationException::withMessages(['email'=>'Compte désactivé.']); }
        $user->tokens()->where('name',$request->device_name)->delete();
        $token = $user->createToken($request->device_name,['ecritures:read','ecritures:write','factures:read','factures:write','tiers:read','tiers:write','dashboard:read']);
        $s = $user->societe;
        return response()->json(['token'=>$token->plainTextToken,'token_type'=>'Bearer','user'=>['id'=>$user->id,'nom_complet'=>$user->nom_complet,'email'=>$user->email,'role'=>$user->role,'societe'=>['id'=>$s->id,'raison_sociale'=>$s->raison_sociale,'devise'=>$s->devise,'taux_tva'=>$s->taux_tva]]]);
    }
    public function logout(Request $request) { $request->user()->currentAccessToken()->delete(); return response()->json(['message'=>'Déconnecté.']); }
    public function tokens(Request $request) { return response()->json($request->user()->tokens()->select('id','name','last_used_at','created_at')->orderByDesc('created_at')->get()); }
    public function revoquer(Request $request, int $id) { $request->user()->tokens()->where('id',$id)->delete(); return response()->json(['message'=>'Token révoqué.']); }
}
