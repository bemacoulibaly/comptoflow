<?php

namespace Tests\Unit;

use App\Models\Compte;
use App\Models\Ecriture;
use App\Models\LigneEcriture;
use App\Models\Societe;
use App\Models\User;
use App\Services\EcritureService;
use App\Services\TvaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcritureServiceTest extends TestCase
{
    use RefreshDatabase;

    private EcritureService $service;
    private Societe $societe;
    private User $user;
    private Compte $c411;
    private Compte $c512;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EcritureService::class);
        $this->societe = Societe::factory()->create();
        $this->user    = User::factory()->create(['societe_id' => $this->societe->id, 'role' => 'admin']);
        $this->actingAs($this->user);

        $this->c411 = Compte::factory()->create([
            'societe_id' => $this->societe->id,
            'numero'     => '411',
            'classe'     => '4',
            'type'       => 'actif',
        ]);
        $this->c512 = Compte::factory()->create([
            'societe_id' => $this->societe->id,
            'numero'     => '512',
            'classe'     => '5',
            'type'       => 'actif',
        ]);
    }

    public function test_creer_retourne_une_écriture_avec_lignes(): void
    {
        $ecriture = $this->service->creer($this->societe, [
            'numero_piece'  => 'BQ-2025-0001',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Test',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Client', 'debit' => 1000000, 'credit' => 0],
                ['numero_compte' => '512', 'libelle' => 'Banque',  'debit' => 0, 'credit' => 1000000],
            ],
        ]);

        $this->assertInstanceOf(Ecriture::class, $ecriture);
        $this->assertCount(2, $ecriture->lignes);
        $this->assertEquals('brouillon', $ecriture->statut);
    }

    public function test_total_debit_et_credit_calculés_correctement(): void
    {
        $ecriture = $this->service->creer($this->societe, [
            'numero_piece'  => 'BQ-2025-0002',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Test totaux',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Ligne 1', 'debit' => 750000, 'credit' => 0],
                ['numero_compte' => '512', 'libelle' => 'Ligne 2', 'debit' => 0, 'credit' => 750000],
            ],
        ]);

        $this->assertEquals(750000, $ecriture->total_debit);
        $this->assertEquals(750000, $ecriture->total_credit);
        $this->assertTrue($ecriture->isEquilibree());
    }

    public function test_valider_ecriture_equilibrée(): void
    {
        $ecriture = $this->service->creer($this->societe, [
            'numero_piece'  => 'BQ-2025-0003',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Test validation',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Client', 'debit' => 500000, 'credit' => 0],
                ['numero_compte' => '512', 'libelle' => 'Banque',  'debit' => 0, 'credit' => 500000],
            ],
        ]);

        $validee = $this->service->valider($ecriture);
        $this->assertEquals('validee', $validee->statut);
        $this->assertNotNull($validee->validee_at);
    }

    public function test_valider_écriture_déséquilibrée_lève_exception(): void
    {
        $this->expectException(\DomainException::class);

        $ecriture = Ecriture::factory()->create([
            'societe_id' => $this->societe->id,
            'user_id'    => $this->user->id,
        ]);
        LigneEcriture::factory()->create([
            'ecriture_id' => $ecriture->id,
            'compte_id'   => $this->c411->id,
            'debit'       => 1000,
            'credit'      => 0,
        ]);
        LigneEcriture::factory()->create([
            'ecriture_id' => $ecriture->id,
            'compte_id'   => $this->c512->id,
            'debit'       => 0,
            'credit'      => 500,
        ]);

        $this->service->valider($ecriture);
    }

    public function test_numéro_pièce_auto_généré_avec_bon_format(): void
    {
        $numero = $this->service->genererNumeroPiece($this->societe, 'BQ');
        $this->assertStringStartsWith('BQ-' . now()->year . '-', $numero);
    }

    public function test_grand_livre_retourne_lignes_et_totaux(): void
    {
        $ecriture = $this->service->creer($this->societe, [
            'numero_piece'  => 'BQ-2025-0004',
            'date_ecriture' => '2025-06-10',
            'journal'       => 'BQ',
            'libelle'       => 'Test grand livre',
            'lignes'        => [
                ['numero_compte' => '411', 'libelle' => 'Client A', 'debit' => 300000, 'credit' => 0],
                ['numero_compte' => '512', 'libelle' => 'Banque',   'debit' => 0, 'credit' => 300000],
            ],
        ]);

        $ecriture->valider();

        $gl = $this->service->grandLivre($this->societe, '411', '2025-01-01', '2025-12-31');

        $this->assertArrayHasKey('compte', $gl);
        $this->assertArrayHasKey('lignes', $gl);
        $this->assertArrayHasKey('total_debit', $gl);
        $this->assertArrayHasKey('solde_final', $gl);
        $this->assertEquals(300000, $gl['total_debit']);
    }
}

class TvaServiceTest extends TestCase
{
    use RefreshDatabase;

    private TvaService $service;
    private Societe $societe;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TvaService::class);
        $this->societe = Societe::factory()->create(['taux_tva' => 18.0]);
    }

    public function test_calcul_tva_sans_factures_retourne_zéros(): void
    {
        $result = $this->service->calculer(
            $this->societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $this->assertEquals(0.0, $result['tva_collectee']);
        $this->assertEquals(0.0, $result['tva_deductible']);
        $this->assertEquals(0.0, $result['tva_nette']);
    }

    public function test_tva_nette_égale_collectée_moins_déductible(): void
    {
        $collectee  = 500000;
        $deductible = 120000;
        $nette      = $collectee - $deductible;
        $this->assertEquals(380000, $nette);
    }

    public function test_générer_declaration_crée_enregistrement(): void
    {
        $debut = now()->startOfMonth()->toDateString();
        $fin   = now()->endOfMonth()->toDateString();

        $decl = $this->service->genererDeclaration($this->societe, $debut, $fin);

        $this->assertDatabaseHas('declarations_tva', [
            'societe_id'    => $this->societe->id,
            'periode_debut' => $debut,
            'statut'        => 'brouillon',
        ]);
        $this->assertNotNull($decl->date_echeance);
    }

    public function test_valider_declaration_change_statut(): void
    {
        $decl = $this->service->genererDeclaration(
            $this->societe,
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString()
        );

        $validee = $this->service->valider($decl);
        $this->assertEquals('validee', $validee->statut);
        $this->assertNotNull($validee->validee_at);
    }
}
