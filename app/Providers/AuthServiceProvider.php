<?php
namespace App\Providers;

use App\Models\DeclarationTva;
use App\Models\Ecriture;
use App\Models\Evenement;
use App\Models\Facture;
use App\Models\Rapprochement;
use App\Models\Societe;
use App\Models\Tiers;
use App\Policies\DeclarationTvaPolicy;
use App\Policies\EcriturePolicy;
use App\Policies\EvenementPolicy;
use App\Policies\FacturePolicy;
use App\Policies\RapprochementPolicy;
use App\Policies\SocietePolicy;
use App\Policies\TiersPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Ecriture::class      => EcriturePolicy::class,
        Facture::class       => FacturePolicy::class,
        Tiers::class         => TiersPolicy::class,
        DeclarationTva::class=> DeclarationTvaPolicy::class,
        Rapprochement::class => RapprochementPolicy::class,
        Societe::class       => SocietePolicy::class,
        Evenement::class     => EvenementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
