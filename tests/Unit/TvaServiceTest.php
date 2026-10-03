<?php

namespace Tests\Unit;

use App\Models\Facture;
use App\Models\Societe;
use App\Models\Tiers;
use App\Models\User;
use App\Services\FactureService;
use App\Services\TvaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TvaServiceTest extends TestCase
{
    use RefreshDatabase;

    private TvaService $service;
    private Societe $societe;
    private Tiers $client;
    private Tiers $fournisseur;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service     = app(TvaService::class);
        $this->societe     = Societe::factory()->create(['taux_tva' => 18.0]);
        $user              = User::factory()->create(['societe_id' => $this->societe->id, 'role' => 'admin']);
        $this->actingAs($user);
        $this->client      = Tiers::factory()->create(['societe_id' => $this->societe->id, 'type' => 'client']);
        $this->fournisseur = Tiers::factory()->create(['societe_id' => $this->societe->id, 'type' => 'fournisseur']);
    }

    public function test_calcul_tva_nette(): void
    {
        // Créer une facture client avec TVA collectée
        Facture::factory()->create([
            'societe_id'    => $this->societe->id,
            'tiers_id'      => $this->client->id,
            'user_id'       => auth()->id(),
            'type'          => 'client',
            'statut'        => 'payee',
            'montant_ht'    => 1000000,
            'montant_tva'   => 180000,
            'montant_ttc'   => 1180000,
            'date_emission' => now()->toDateString(),
        ]);

        // Créer une facture fournisseur avec TVA déductible
        Facture::factory()->create([
            'societe_id'    => $this->societe->id,
            'tiers_id'      => $this->fournisseur->id,
            'user_id'       => auth()->id(),
            'type'          => 'fournisseur',
            'statut'        => 'payee',
            'montant_ht'    => 500000,
            'montant_tva'   => 90000,
            'montant_ttc'   => 590000,
            'date_emission' => now()->toDateString(),
        ]);

        $totaux = $this->service->calculer(
            $this->societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $this->assertEquals(180000, $totaux['tva_collectee']);
        $this->assertEquals(90000,  $totaux['tva_deductible']);
        $this->assertEquals(90000,  $totaux['tva_nette']);
    }

    public function test_tva_nette_nulle_si_aucune_facture(): void
    {
        $totaux = $this->service->calculer(
            $this->societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $this->assertEquals(0, $totaux['tva_collectee']);
        $this->assertEquals(0, $totaux['tva_deductible']);
        $this->assertEquals(0, $totaux['tva_nette']);
    }

    public function test_génération_déclaration(): void
    {
        $decl = $this->service->genererDeclaration(
            $this->societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $this->assertEquals('brouillon', $decl->statut);
        $this->assertNotNull($decl->date_echeance);
    }

    public function test_validation_déclaration(): void
    {
        $decl = $this->service->genererDeclaration(
            $this->societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $this->service->valider($decl);
        $this->assertEquals('validee', $decl->fresh()->statut);
        $this->assertNotNull($decl->fresh()->validee_at);
    }

    public function test_historique_retourne_collection(): void
    {
        $this->service->genererDeclaration($this->societe, '2025-01-01', '2025-01-31');
        $this->service->genererDeclaration($this->societe, '2025-02-01', '2025-02-28');

        $hist = $this->service->historique($this->societe);
        $this->assertCount(2, $hist);
    }
}

// ─────────────────────────────────────────────────────────────
class FactureServiceTest extends TestCase
{
    use RefreshDatabase;

    private FactureService $service;
    private Societe $societe;
    private Tiers $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FactureService::class);
        $this->societe = Societe::factory()->create(['taux_tva' => 18.0]);
        $user          = User::factory()->create(['societe_id' => $this->societe->id, 'role' => 'admin']);
        $this->actingAs($user);
        $this->client  = Tiers::factory()->create(['societe_id' => $this->societe->id, 'type' => 'client']);
    }

    public function test_calcul_montants_corrects(): void
    {
        $facture = $this->service->creer($this->societe, [
            'tiers_id'      => $this->client->id,
            'type'          => 'client',
            'date_emission' => now()->toDateString(),
            'date_echeance' => now()->addDays(30)->toDateString(),
            'taux_tva'      => 18.0,
            'lignes'        => [
                ['designation' => 'Ligne 1', 'quantite' => 3, 'prix_unitaire' => 200000, 'taux_tva' => 18.0],
                ['designation' => 'Ligne 2', 'quantite' => 1, 'prix_unitaire' => 150000, 'taux_tva' => 18.0],
            ],
        ]);

        $this->assertEquals(750000,  $facture->montant_ht);   // 600000 + 150000
        $this->assertEquals(135000,  $facture->montant_tva);  // 750000 * 18%
        $this->assertEquals(885000,  $facture->montant_ttc);
        $this->assertEquals(0,       $facture->montant_paye);
        $this->assertEquals(885000,  $facture->solde);
    }

    public function test_statut_passe_à_payee_après_paiement_total(): void
    {
        $facture = $this->service->creer($this->societe, [
            'tiers_id'      => $this->client->id,
            'type'          => 'client',
            'date_emission' => now()->toDateString(),
            'date_echeance' => now()->addDays(30)->toDateString(),
            'taux_tva'      => 18.0,
            'lignes'        => [['designation' => 'P', 'quantite' => 1, 'prix_unitaire' => 500000, 'taux_tva' => 18.0]],
        ]);

        $this->service->enregistrerPaiement($facture, 590000);
        $this->assertEquals('payee', $facture->fresh()->statut);
        $this->assertEquals(0,       $facture->fresh()->solde);
    }

    public function test_numéro_fournisseur_préfixe_ff(): void
    {
        $n = $this->service->genererNumero($this->societe, 'fournisseur');
        $this->assertStringStartsWith('FF-', $n);
    }

    public function test_stats_période(): void
    {
        $this->service->creer($this->societe, [
            'tiers_id'      => $this->client->id,
            'type'          => 'client',
            'date_emission' => now()->toDateString(),
            'date_echeance' => now()->addDays(30)->toDateString(),
            'taux_tva'      => 18.0,
            'lignes'        => [['designation' => 'P', 'quantite' => 1, 'prix_unitaire' => 1000000, 'taux_tva' => 18.0]],
        ]);

        $stats = $this->service->statsParPeriode(
            $this->societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $this->assertEquals(1000000, $stats['recettes_ht']);
        $this->assertEquals(180000,  $stats['tva_collectee']);
    }
}
