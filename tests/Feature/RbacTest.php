<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Societe;
use App\Models\Tiers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    private Societe $societe;
    private User $admin;
    private User $editeur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->societe = Societe::factory()->create();
        $this->admin   = User::factory()->create([
            'societe_id' => $this->societe->id,
            'role'       => 'admin',
            'actif'      => true,
        ]);
        $this->editeur = User::factory()->create([
            'societe_id' => $this->societe->id,
            'role'       => 'editeur',
            'actif'      => true,
        ]);
    }

    // ── Garantie centrale : l'admin n'est jamais bloqué ──────

    public function test_admin_accede_a_toutes_les_pages_meme_avec_un_role_personnalise_tres_restrictif(): void
    {
        $roleRestrictif = Role::create([
            'societe_id' => $this->societe->id, 'nom' => 'Ultra restrictif',
            'slug' => 'ultra-restrictif', 'couleur' => 'red', 'est_systeme' => false,
        ]);
        foreach (array_keys(Role::PAGES) as $page) {
            RolePermission::create(['role_id' => $roleRestrictif->id, 'page' => $page, 'autorise' => false]);
        }

        // Même si on assigne ce rôle à l'admin par erreur, isAdmin() doit toujours primer
        $this->admin->update(['role_id' => $roleRestrictif->id]);

        foreach (array_keys(Role::PAGES) as $page) {
            $this->assertTrue($this->admin->fresh()->peutAccederA($page), "L'admin doit garder accès à '$page' même avec un rôle restrictif assigné.");
        }
    }

    public function test_admin_peut_toujours_acceder_a_la_page_tiers_verrouillee_pour_les_autres(): void
    {
        $roleSansTiers = Role::create([
            'societe_id' => $this->societe->id, 'nom' => 'Sans tiers',
            'slug' => 'sans-tiers', 'couleur' => 'amber', 'est_systeme' => false,
        ]);
        RolePermission::create(['role_id' => $roleSansTiers->id, 'page' => 'tiers', 'autorise' => false]);

        $this->editeur->update(['role_id' => $roleSansTiers->id]);

        $this->actingAs($this->admin)
            ->get(route('tiers.index'))
            ->assertOk();
    }

    // ── Verrouillage de page par rôle personnalisé ───────────

    public function test_un_role_personnalise_bloque_bien_l_acces_a_une_page_entiere(): void
    {
        $stagiaire = Role::create([
            'societe_id' => $this->societe->id, 'nom' => 'Stagiaire',
            'slug' => 'stagiaire', 'couleur' => 'gray', 'est_systeme' => false,
        ]);
        RolePermission::create(['role_id' => $stagiaire->id, 'page' => 'tiers', 'autorise' => false]);
        RolePermission::create(['role_id' => $stagiaire->id, 'page' => 'factures', 'autorise' => true]);

        $compteStagiaire = User::factory()->create([
            'societe_id' => $this->societe->id, 'role' => 'editeur',
            'role_id'    => $stagiaire->id, 'actif' => true,
        ]);

        $this->actingAs($compteStagiaire)
            ->get(route('tiers.index'))
            ->assertForbidden();

        $this->actingAs($compteStagiaire)
            ->get(route('factures.index'))
            ->assertOk();
    }

    public function test_compte_sans_role_personnalise_garde_le_comportement_legacy(): void
    {
        // editeur sans role_id : accès à tout sauf paramètres (comportement historique)
        $this->actingAs($this->editeur)
            ->get(route('tiers.index'))
            ->assertOk();

        $this->actingAs($this->editeur)
            ->get(route('parametres.index'))
            ->assertForbidden();
    }

    // ── Rôles personnalisés : CRUD ───────────────────────────

    public function test_admin_peut_creer_un_role_personnalise_avec_nom_libre(): void
    {
        $this->actingAs($this->admin)
            ->post(route('parametres.roles.store'), [
                'nom'     => 'Comptable senior',
                'couleur' => 'purple',
                'pages'   => ['dashboard', 'ecritures', 'factures'],
            ])
            ->assertRedirect(route('parametres.roles.index'));

        $this->assertDatabaseHas('roles', [
            'societe_id'  => $this->societe->id,
            'nom'         => 'Comptable senior',
            'est_systeme' => false,
        ]);

        $role = Role::where('nom', 'Comptable senior')->first();
        $this->assertTrue($role->peutAccederA('ecritures'));
        $this->assertFalse($role->peutAccederA('tiers')); // pas cochée → false explicite
    }

    public function test_non_admin_ne_peut_pas_creer_de_role(): void
    {
        $this->actingAs($this->editeur)
            ->post(route('parametres.roles.store'), [
                'nom' => 'Test', 'couleur' => 'blue', 'pages' => [],
            ])
            ->assertForbidden();
    }

    public function test_role_systeme_ne_peut_pas_etre_modifie(): void
    {
        $roleAdmin = Role::create([
            'societe_id' => $this->societe->id, 'nom' => 'Administrateur',
            'slug' => 'admin', 'couleur' => 'green', 'est_systeme' => true,
        ]);

        $this->actingAs($this->admin)
            ->put(route('parametres.roles.update', $roleAdmin), [
                'nom' => 'Hacké', 'couleur' => 'red', 'pages' => [],
            ])
            ->assertForbidden();

        $this->assertEquals('Administrateur', $roleAdmin->fresh()->nom);
    }

    public function test_role_systeme_ne_peut_pas_etre_supprime(): void
    {
        $roleEditeur = Role::create([
            'societe_id' => $this->societe->id, 'nom' => 'Éditeur',
            'slug' => 'editeur', 'couleur' => 'blue', 'est_systeme' => true,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('parametres.roles.destroy', $roleEditeur))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('roles', ['id' => $roleEditeur->id]);
    }

    public function test_role_assigne_a_des_comptes_ne_peut_pas_etre_supprime(): void
    {
        $role = Role::create([
            'societe_id' => $this->societe->id, 'nom' => 'Stagiaire',
            'slug' => 'stagiaire', 'couleur' => 'gray', 'est_systeme' => false,
        ]);
        User::factory()->create(['societe_id' => $this->societe->id, 'role_id' => $role->id]);

        $this->actingAs($this->admin)
            ->delete(route('parametres.roles.destroy', $role))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    // ── Journal d'activité (visibilité asymétrique) ──────────

    public function test_action_sur_un_tiers_genere_une_entree_dans_le_journal(): void
    {
        $this->actingAs($this->editeur)
            ->post(route('tiers.store'), [
                'type' => 'client', 'nom' => 'Client Test SARL',
            ]);

        $this->assertDatabaseHas('audit_trails', [
            'societe_id' => $this->societe->id,
            'user_id'    => $this->editeur->id,
            'evenement'  => 'cree',
            'sujet_type' => Tiers::class,
        ]);
    }

    public function test_admin_voit_le_journal_global_de_la_societe(): void
    {
        $this->actingAs($this->editeur)->post(route('tiers.store'), ['type' => 'client', 'nom' => 'ABC']);

        $this->actingAs($this->admin)
            ->get(route('parametres.journal'))
            ->assertOk()
            ->assertSee('ABC', false);
    }

    public function test_non_admin_ne_peut_pas_voir_le_journal_global(): void
    {
        $this->actingAs($this->editeur)
            ->get(route('parametres.journal'))
            ->assertForbidden();
    }

    public function test_non_admin_ne_peut_pas_voir_l_activite_d_un_autre_compte(): void
    {
        $this->actingAs($this->editeur)
            ->get(route('parametres.utilisateurs.activite', $this->admin))
            ->assertForbidden();
    }

    public function test_admin_voit_l_activite_detaillee_d_un_collaborateur(): void
    {
        $this->actingAs($this->editeur)->post(route('tiers.store'), ['type' => 'client', 'nom' => 'XYZ Corp']);

        $this->actingAs($this->admin)
            ->get(route('parametres.utilisateurs.activite', $this->editeur))
            ->assertOk()
            ->assertSee('XYZ Corp', false);
    }

    // ── Activation / désactivation de compte ─────────────────

    public function test_admin_peut_desactiver_puis_reactiver_un_compte(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('parametres.utilisateurs.statut', $this->editeur))
            ->assertRedirect();
        $this->assertFalse($this->editeur->fresh()->actif);

        $this->actingAs($this->admin)
            ->patch(route('parametres.utilisateurs.statut', $this->editeur))
            ->assertRedirect();
        $this->assertTrue($this->editeur->fresh()->actif);
    }

    public function test_compte_desactive_ne_peut_pas_se_connecter(): void
    {
        $this->editeur->update(['actif' => false]);

        $this->post(route('login'), [
            'email' => $this->editeur->email, 'password' => 'password',
        ])->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_compte_desactive_pendant_une_session_active_est_deconnecte_au_prochain_acces(): void
    {
        $this->actingAs($this->editeur);
        $this->editeur->update(['actif' => false]);

        $this->get(route('dashboard'));

        $this->assertGuest();
    }
}
