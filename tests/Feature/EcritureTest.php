<?php

namespace Tests\Feature;

use App\Models\Compte;
use App\Models\Ecriture;
use App\Models\LigneEcriture;
use App\Models\Societe;
use App\Models\User;
use App\Services\EcritureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcritureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Societe $societe;
    private Compte $compte411;
    private Compte $compte512;

    protected function setUp(): void
    {
        parent::setUp();

        $this->societe = Societe::factory()->create();
        $this->admin   = User::factory()->create([
            'societe_id' => $this->societe->id,
            'role'       => 'admin',
        ]);

        $this->compte411 = Compte::factory()->create([
            'societe_id' => $this->societe->id,
            'numero'     => '411',
            'libelle'    => 'Clients',
            'classe'     => '4',
            'type'       => 'actif',
        ]);

        $this->compte512 = Compte::factory()->create([
            'societe_id' => $this->societe->id,
            'numero'     => '512',
            'libelle'    => 'Banque',
            'classe'     => '5',
            'type'       => 'actif',
        ]);
    }

    // ── Accès ──────────────────────────────────────────────

    public function test_invité_redirigé_vers_login(): void
    {
        $this->get(route('ecritures.index'))->assertRedirect(route('login'));
    }

    public function test_admin_peut_accéder_à_la_liste(): void
    {
        $this->actingAs($this->admin)
            ->get(route('ecritures.index'))
            ->assertOk()
            ->assertViewIs('ecritures.index');
    }

    // ── Création ──────────────────────────────────────────

    public function test_création_écriture_équilibrée(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'numero_piece'  => 'BQ-2025-0001',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Encaissement client',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Client Tana', 'debit' => 1200000, 'credit' => 0],
                ['numero_compte' => '512', 'libelle' => 'Banque',      'debit' => 0, 'credit' => 1200000],
            ],
        ];

        $response = $this->post(route('ecritures.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('ecritures', [
            'numero_piece' => 'BQ-2025-0001',
            'statut'       => 'brouillon',
        ]);
        $this->assertDatabaseCount('lignes_ecriture', 2);
    }

    public function test_écriture_déséquilibrée_rejetée(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'numero_piece'  => 'BQ-2025-0002',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Test déséquilibré',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Client', 'debit' => 500000, 'credit' => 0],
                ['numero_compte' => '512', 'libelle' => 'Banque',  'debit' => 0, 'credit' => 600000],
            ],
        ];

        $this->post(route('ecritures.store'), $payload)
            ->assertSessionHasErrors('lignes');

        $this->assertDatabaseMissing('ecritures', ['numero_piece' => 'BQ-2025-0002']);
    }

    public function test_écriture_avec_une_seule_ligne_rejetée(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'numero_piece'  => 'BQ-2025-0003',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Test une ligne',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Client', 'debit' => 500000, 'credit' => 500000],
            ],
        ];

        $this->post(route('ecritures.store'), $payload)
            ->assertSessionHasErrors('lignes');
    }

    // ── Validation ────────────────────────────────────────

    public function test_validation_écriture_brouillon(): void
    {
        $this->actingAs($this->admin);

        $service  = app(EcritureService::class);
        $ecriture = $service->creer($this->societe, [
            'numero_piece'  => 'BQ-2025-0010',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Test validation',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Client', 'debit' => 500000, 'credit' => 0],
                ['numero_compte' => '512', 'libelle' => 'Banque',  'debit' => 0, 'credit' => 500000],
            ],
        ]);

        $this->assertEquals('brouillon', $ecriture->statut);

        $this->post(route('ecritures.valider', $ecriture))
            ->assertRedirect();

        $this->assertEquals('validee', $ecriture->fresh()->statut);
    }

    public function test_lecteur_ne_peut_pas_créer_décriture(): void
    {
        $lecteur = User::factory()->create([
            'societe_id' => $this->societe->id,
            'role'       => 'lecteur',
        ]);

        $this->actingAs($lecteur)
            ->post(route('ecritures.store'), [
                'numero_piece'  => 'BQ-2025-0099',
                'date_ecriture' => '2025-06-10',
                'journal'       => 'BQ',
                'libelle'       => 'Test',
                'lignes'        => [
                    ['numero_compte' => '411', 'libelle' => 'x', 'debit' => 100, 'credit' => 0],
                    ['numero_compte' => '512', 'libelle' => 'y', 'debit' => 0, 'credit' => 100],
                ],
            ])
            ->assertForbidden();
    }
}
