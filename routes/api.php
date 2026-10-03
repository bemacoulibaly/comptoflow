<?php
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CompteApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\EcritureApiController;
use App\Http\Controllers\Api\FactureApiController;
use App\Http\Controllers\Api\OcrApiController;
use App\Http\Controllers\Api\TiersApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {

    Route::post('/auth/login', [AuthApiController::class, 'login'])->name('api.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout',        [AuthApiController::class, 'logout']);
        Route::get('/auth/tokens',         [AuthApiController::class, 'tokens']);
        Route::delete('/auth/tokens/{id}', [AuthApiController::class, 'revoquer']);

        Route::get('/me', function (Request $request) {
            $u = $request->user(); $s = $u->societe;
            return response()->json([
                'user'    => ['id'=>$u->id,'nom_complet'=>$u->nom_complet,'email'=>$u->email,'role'=>$u->role,'initiales'=>$u->initiales],
                'societe' => ['id'=>$s->id,'raison_sociale'=>$s->raison_sociale,'devise'=>$s->devise,'taux_tva'=>$s->taux_tva],
            ]);
        });

        Route::get('/dashboard', [DashboardApiController::class, 'index']);

        Route::prefix('ecritures')->group(function () {
            Route::get('/',                    [EcritureApiController::class, 'index']);
            Route::post('/',                   [EcritureApiController::class, 'store']);
            Route::get('/{ecriture}',          [EcritureApiController::class, 'show']);
            Route::post('/{ecriture}/valider', [EcritureApiController::class, 'valider']);
        });

        Route::prefix('factures')->group(function () {
            Route::get('/en-retard',           [FactureApiController::class, 'enRetard']);
            Route::get('/',                    [FactureApiController::class, 'index']);
            Route::post('/',                   [FactureApiController::class, 'store']);
            Route::get('/{facture}',           [FactureApiController::class, 'show']);
            Route::post('/{facture}/paiement', [FactureApiController::class, 'paiement']);
        });

        Route::prefix('tiers')->group(function () {
            Route::get('/',        [TiersApiController::class, 'index']);
            Route::post('/',       [TiersApiController::class, 'store']);
            Route::get('/{tiers}', [TiersApiController::class, 'show']);
        });

        Route::get('/comptes', [CompteApiController::class, 'index']);
        Route::post('/ocr/facture', [OcrApiController::class, 'extraire']);
    });
});
