<?php
namespace App\Providers;

use App\Models\Ecriture;
use App\Models\Societe;
use App\Services\DashboardService;
use App\Services\EcritureService;
use App\Services\FactureService;
use App\Services\RapprochementService;
use App\Services\TvaService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EcritureService::class);
        $this->app->singleton(FactureService::class);
        $this->app->singleton(TvaService::class);
        $this->app->singleton(RapprochementService::class);
        $this->app->singleton(DashboardService::class);
    }

    public function boot(): void
    {
        // Partager le compteur d'écritures brouillon à toutes les vues
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $brouillons = Ecriture::where('societe_id', auth()->user()->societe_id)
                    ->where('statut', 'brouillon')
                    ->count();
                $view->with('ecrituresBrouillon', $brouillons);
            }
        });
    }
}
