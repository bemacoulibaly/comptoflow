<?php

use App\Http\Controllers\BilanController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EcritureController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ImportBancaireController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapprochementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TiersController;
use App\Http\Controllers\TvaController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// ── Authentification ─────────────────────────────────────────
require __DIR__ . '/auth.php';

// ── Application (authentifié) ────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Documents justificatifs ───────────────────────────────
    Route::post('/documents',                        [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download',     [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}',           [DocumentController::class, 'destroy'])->name('documents.destroy');

    // ── Import bancaire ───────────────────────────────────────
    Route::prefix('import')->name('import.')->middleware('page:ecritures')->group(function () {
        Route::get('/',    [ImportBancaireController::class, 'create'])->name('create');
        Route::post('/',   [ImportBancaireController::class, 'store'])->name('store');
        Route::post('/confirmer', [ImportBancaireController::class, 'confirmer'])->name('confirmer');
    });

    // ── IA suggestion de compte ───────────────────────────────
    Route::post('/ia/suggerer-compte', [\App\Http\Controllers\IaSuggestionController::class, 'suggerer'])
        ->name('ia.suggerer-compte');

    // ── Assistant vocal ───────────────────────────────────────
    Route::post('/assistant/vocal', [\App\Http\Controllers\AssistantVocalController::class, 'repondre'])
        ->name('assistant.vocal');

    // ── Prévisions de trésorerie ──────────────────────────────
    Route::get('/previsions', [\App\Http\Controllers\PrevisionController::class, 'index'])
        ->name('previsions.index')
        ->middleware('page:bilan');

    // Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('page:dashboard');

    // ── Écritures ────────────────────────────────────────────
    Route::prefix('ecritures')->name('ecritures.')->middleware('page:ecritures')->group(function () {
        Route::get('/',                            [EcritureController::class, 'index'])->name('index');
        Route::get('/creer',                       [EcritureController::class, 'create'])->name('create');
        Route::post('/',                           [EcritureController::class, 'store'])->name('store');
        Route::get('/rapport/grand-livre',         [EcritureController::class, 'grandLivre'])->name('grand-livre');
        Route::get('/{ecriture}',                  [EcritureController::class, 'show'])->name('show');
        Route::post('/{ecriture}/valider',         [EcritureController::class, 'valider'])->name('valider');
    });

    // ── Factures ─────────────────────────────────────────────
    Route::prefix('factures')->name('factures.')->middleware('page:factures')->group(function () {
        Route::get('/',                            [FactureController::class, 'index'])->name('index');
        Route::get('/creer',                       [FactureController::class, 'create'])->name('create');
        Route::post('/',                           [FactureController::class, 'store'])->name('store');
        Route::get('/{facture}',                   [FactureController::class, 'show'])->name('show');
        Route::post('/{facture}/paiement',         [FactureController::class, 'paiement'])->name('paiement');
        Route::post('/{facture}/annuler',          [FactureController::class, 'annuler'])->name('annuler');
    });

    // ── Rapprochement ─────────────────────────────────────────
    Route::prefix('rapprochement')->name('rapprochement.')->middleware('page:rapprochement')->group(function () {
        Route::get('/',                            [RapprochementController::class, 'index'])->name('index');
        Route::get('/creer',                       [RapprochementController::class, 'create'])->name('create');
        Route::post('/',                           [RapprochementController::class, 'store'])->name('store');
        Route::get('/{rapprochement}',             [RapprochementController::class, 'show'])->name('show');
        Route::patch('/ligne/{ligne}/pointer',          [RapprochementController::class, 'pointer'])->name('pointer');
        Route::get('/{rapprochement}/suggestions',       [RapprochementController::class, 'suggestions'])->name('suggestions');
        Route::post('/{rapprochement}/appliquer',        [RapprochementController::class, 'appliquerSuggestion'])->name('appliquer');
        Route::post('/{rapprochement}/valider',    [RapprochementController::class, 'valider'])->name('valider');
    });

    // ── Rapports ──────────────────────────────────────────────
    Route::prefix('rapports')->name('rapports.')->middleware('page:bilan')->group(function () {
        Route::get('/bilan',                       [BilanController::class, 'index'])->name('bilan');
        Route::get('/resultat',                    [BilanController::class, 'resultat'])->name('resultat');
    });

    // ── TVA ───────────────────────────────────────────────────
    Route::prefix('tva')->name('tva.')->middleware('page:tva')->group(function () {
        Route::get('/',                            [TvaController::class, 'index'])->name('index');
        Route::post('/generer',                    [TvaController::class, 'generer'])->name('generer');
        Route::get('/{declaration}',               [TvaController::class, 'show'])->name('show');
        Route::post('/{declaration}/valider',      [TvaController::class, 'valider'])->name('valider');
    });

    // ── Tiers ─────────────────────────────────────────────────
    Route::middleware('page:tiers')->group(function () {
        Route::resource('tiers', TiersController::class);
    });

    // ── Calendrier ────────────────────────────────────────────
    Route::prefix('calendrier')->name('calendrier.')->middleware('page:calendrier')->group(function () {
        Route::get('/',                            [CalendrierController::class, 'index'])->name('index');
        Route::post('/',                           [CalendrierController::class, 'store'])->name('store');
        Route::delete('/{evenement}',              [CalendrierController::class, 'destroy'])->name('destroy');
    });

    // ── Mon profil (page individuelle, jamais verrouillable) ──
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/',           [ProfileController::class, 'show'])->name('show');
        Route::patch('/',         [ProfileController::class, 'update'])->name('update');
        Route::put('/password',   [ProfileController::class, 'updatePassword'])->name('password');
    });

    // ── Paramètres (admin uniquement, cf. middleware interne) ──
    Route::prefix('parametres')->name('parametres.')->middleware('page:parametres')->group(function () {
        Route::get('/',                            [ParametresController::class, 'index'])->name('index');
        Route::patch('/societe',                   [ParametresController::class, 'updateSociete'])->name('societe');

        // Gestion des comptes
        Route::prefix('utilisateurs')->name('utilisateurs.')->group(function () {
            Route::get('/',                        [UserManagementController::class, 'index'])->name('index');
            Route::get('/creer',                   [UserManagementController::class, 'create'])->name('create');
            Route::post('/',                       [UserManagementController::class, 'store'])->name('store');
            Route::get('/{user}',                  [UserManagementController::class, 'show'])->name('show');
            Route::patch('/{user}/role',           [UserManagementController::class, 'updateRole'])->name('role');
            Route::put('/{user}/password',         [UserManagementController::class, 'resetPassword'])->name('password');
            Route::patch('/{user}/statut',         [UserManagementController::class, 'toggleStatut'])->name('statut');
            Route::delete('/{user}',               [UserManagementController::class, 'destroy'])->name('destroy');

            // Activité d'un utilisateur précis (log + audit), visible par l'admin uniquement
            Route::get('/{user}/activite',         [UserManagementController::class, 'activite'])->name('activite');
        });

        // Gestion des rôles personnalisés et de leurs permissions de page
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/',                        [RoleController::class, 'index'])->name('index');
            Route::get('/creer',                   [RoleController::class, 'create'])->name('create');
            Route::post('/',                       [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/editer',           [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}',                  [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}',                [RoleController::class, 'destroy'])->name('destroy');
        });

        // Journal d'activité global de la société (admin uniquement)
        Route::get('/journal',                     [UserManagementController::class, 'journal'])->name('journal');
    });
});
