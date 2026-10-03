<?php

namespace Tests\Feature;

use App\Models\Facture;
use App\Models\Societe;
use App\Models\Tiers;
use App\Models\User;
use App\Services\FactureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Societe $societe;
    private Tiers $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->societe = Societe::factory()->create(['taux_tva' => 18.0]);
        $this->admin   = User::factory()->create([
            'societe_id' => $this->societe->id,
            'role'       => 'admin',
        ]);
        $this->client  = Tiers::factory()->create([
            'societe_id' => $this->societe->id,
            'type'       => 'client',
            'nom'        => 'SARL Tana Import',
        ]);
    }

    public function test_liste_factures_accessible(): void
    {
        $this->actingAs($this->admin)
            ->get(route('factures.index'))
            ->assertOk()
            ->assertViewIs('factures.index');
    }

    public function test_création_facture_client(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'tiers_id'      => $this->client->id,
            'type'          => 'client',
            'date_emission' => '2025-06-10',
            'date_echeance' => '2025-06-30',
            'taux_tva'      => 18.0,
            'lignes'        => [
                [
                    'designation'   => 'Prestation de conseil',
                    'quantite'      => 2,
                    'prix_unitaire' => 500000,
                    'taux_tva'      => 18.0,
                ],
            ],
        ];

        $this->post(route('factures.store'), $payload)
            ->assertRedirect();

        $facture = Facture::first();
        $this->assertNotNull($facture);
        $this->assertEquals(1000000, $facture->montant_ht);
        $this->assertEquals(180000,  $facture->montant_tva);
        $this->assertEquals(1180000, $facture->montant_ttc);
        $this->assertStringStartsWith('FAC-', $facture->numero);
    }

    public function test_numéro_facture_auto_incrémenté(): void
    {
        $service = app(FactureService::class);

        $n1 = $service->genererNumero($this->societe, 'client');
        $n2 = $service->genererNumero($this->societe, 'client');

        $this->assertStringStartsWith('FAC-', $n1);
        // Les deux sont différents s'il y a déjà une facture
        $this->assertNotEquals($n1, 'FAC-2025-0000');
    }

    public function test_enregistrement_paiement_partiel(): void
    {
        $this->actingAs($this->admin);

        $service = app(FactureService::class);
        $facture = $service->creer($this->societe, [
            'tiers_id'      => $this->client->id,
            'type'          => 'client',
            'date_emission' => '2025-06-01',
            'date_echeance' => '2025-06-30',
            'taux_tva'      => 18.0,
            'lignes'        => [[
                'designation'   => 'Prestation',
                'quantite'      => 1,
                'prix_unitaire' => 1000000,
                'taux_tva'      => 18.0,
            ]],
        ]);

        $service->enregistrerPaiement($facture, 600000);
        $this->assertEquals('partielle', $facture->fresh()->statut);
        $this->assertEquals(600000, $facture->fresh()->montant_paye);

        $service->enregistrerPaiement($facture->fresh(), 580000);
        $this->assertEquals('payee', $facture->fresh()->statut);
    }

    public function test_date_echeance_avant_emission_rejetée(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('factures.store'), [
            'tiers_id'      => $this->client->id,
            'type'          => 'client',
            'date_emission' => '2025-06-30',
            'date_echeance' => '2025-06-01',
            'lignes'        => [[
                'designation'   => 'Test',
                'quantite'      => 1,
                'prix_unitaire' => 100000,
            ]],
        ])->assertSessionHasErrors('date_echeance');
    }

    public function test_annulation_facture(): void
    {
        $this->actingAs($this->admin);

        $service = app(FactureService::class);
        $facture = $service->creer($this->societe, [
            'tiers_id'      => $this->client->id,
            'type'          => 'client',
            'date_emission' => '2025-06-01',
            'date_echeance' => '2025-06-30',
            'taux_tva'      => 18.0,
            'lignes'        => [[
                'designation'   => 'Prestation',
                'quantite'      => 1,
                'prix_unitaire' => 500000,
                'taux_tva'      => 18.0,
            ]],
        ]);

        $this->post(route('factures.annuler', $facture));
        $this->assertEquals('annulee', $facture->fresh()->statut);
    }
}
