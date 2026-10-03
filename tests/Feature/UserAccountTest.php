<?php

namespace Tests\Feature;

use App\Models\Societe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccountTest extends TestCase
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
        ]);
        $this->editeur = User::factory()->create([
            'societe_id' => $this->societe->id,
            'role'       => 'editeur',
        ]);
    }

    // ── Page profil individuel ──────────────────────────────

    public function test_un_utilisateur_accede_a_son_propre_profil(): void
    {
        $this->actingAs($this->editeur)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee($this->editeur->nom_complet)
            ->assertSee($this->editeur->email);
    }

    public function test_un_utilisateur_peut_modifier_ses_informations(): void
    {
        $this->actingAs($this->editeur)
            ->patch(route('profile.update'), [
                'prenom' => 'Nouveau',
                'nom'    => 'Nom',
                'email'  => $this->editeur->email,
            ])
            ->assertRedirect();

        $this->assertEquals('Nouveau', $this->editeur->fresh()->prenom);
    }

    public function test_un_utilisateur_peut_changer_son_mot_de_passe(): void
    {
        $this->actingAs($this->editeur)
            ->put(route('profile.password'), [
                'current_password'     => 'password', // mot de passe par défaut de la factory
                'password'              => 'nouveaumotdepasse123',
                'password_confirmation' => 'nouveaumotdepasse123',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function test_changement_mot_de_passe_refuse_si_actuel_incorrect(): void
    {
        $this->actingAs($this->editeur)
            ->put(route('profile.password'), [
                'current_password'     => 'mauvais_mot_de_passe',
                'password'              => 'nouveaumotdepasse123',
                'password_confirmation' => 'nouveaumotdepasse123',
            ])
            ->assertSessionHasErrors('current_password');
    }

    // ── Gestion des comptes — admin uniquement ───────────────

    public function test_admin_accede_a_la_liste_des_comptes(): void
    {
        $this->actingAs($this->admin)
            ->get(route('parametres.utilisateurs.index'))
            ->assertOk()
            ->assertSee($this->editeur->nom_complet);
    }

    public function test_non_admin_ne_peut_pas_acceder_a_la_gestion_des_comptes(): void
    {
        $this->actingAs($this->editeur)
            ->get(route('parametres.utilisateurs.index'))
            ->assertForbidden();
    }

    public function test_admin_peut_creer_un_compte(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            'prenom'                => 'Awa',
            'nom'                   => 'Diabaté',
            'email'                 => 'awa.diabate@cabinet.ci',
            'role'                  => 'editeur',
            'password'              => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ];

        $this->post(route('parametres.utilisateurs.store'), $payload)
            ->assertRedirect(route('parametres.utilisateurs.index'));

        $this->assertDatabaseHas('users', [
            'email'      => 'awa.diabate@cabinet.ci',
            'role'       => 'editeur',
            'societe_id' => $this->societe->id,
        ]);

        // Le nouveau compte peut se connecter avec le mot de passe choisi
        $this->post('/logout');
        $this->post(route('login'), [
            'email'    => 'awa.diabate@cabinet.ci',
            'password' => 'motdepasse123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_non_admin_ne_peut_pas_creer_de_compte(): void
    {
        $this->actingAs($this->editeur);

        $this->post(route('parametres.utilisateurs.store'), [
            'prenom'                => 'Test',
            'nom'                   => 'Test',
            'email'                 => 'test@cabinet.ci',
            'role'                  => 'lecteur',
            'password'              => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'test@cabinet.ci']);
    }

    public function test_creation_compte_email_deja_utilise_rejetee(): void
    {
        $this->actingAs($this->admin)
            ->post(route('parametres.utilisateurs.store'), [
                'prenom'                => 'Doublon',
                'nom'                   => 'Test',
                'email'                 => $this->editeur->email, // déjà utilisé
                'role'                  => 'lecteur',
                'password'              => 'motdepasse123',
                'password_confirmation' => 'motdepasse123',
            ])
            ->assertSessionHasErrors('email');
    }

    // ── Page individuelle vue par l'admin ────────────────────

    public function test_admin_voit_la_page_individuelle_dun_collaborateur(): void
    {
        $this->actingAs($this->admin)
            ->get(route('parametres.utilisateurs.show', $this->editeur))
            ->assertOk()
            ->assertSee($this->editeur->nom_complet)
            ->assertSee($this->editeur->email);
    }

    public function test_admin_peut_changer_le_role_dun_collaborateur(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('parametres.utilisateurs.role', $this->editeur), [
                'role' => 'lecteur',
            ])
            ->assertRedirect();

        $this->assertEquals('lecteur', $this->editeur->fresh()->role);
    }

    public function test_admin_ne_peut_pas_changer_son_propre_role(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('parametres.utilisateurs.role', $this->admin), [
                'role' => 'lecteur',
            ])
            ->assertSessionHasErrors('role');

        $this->assertEquals('admin', $this->admin->fresh()->role);
    }

    public function test_admin_peut_reinitialiser_le_mot_de_passe_dun_collaborateur(): void
    {
        $this->actingAs($this->admin)
            ->put(route('parametres.utilisateurs.password', $this->editeur), [
                'password'              => 'motdepasseinitial123',
                'password_confirmation' => 'motdepasseinitial123',
            ])
            ->assertRedirect();

        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('motdepasseinitial123', $this->editeur->fresh()->password)
        );
    }

    public function test_admin_peut_desactiver_un_compte(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('parametres.utilisateurs.destroy', $this->editeur))
            ->assertRedirect(route('parametres.utilisateurs.index'));

        $this->assertSoftDeleted('users', ['id' => $this->editeur->id]);
    }

    public function test_admin_ne_peut_pas_desactiver_son_propre_compte(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('parametres.utilisateurs.destroy', $this->admin))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id, 'deleted_at' => null]);
    }

    public function test_un_compte_dune_autre_societe_est_inaccessible(): void
    {
        $autreSociete = Societe::factory()->create();
        $autreUser    = User::factory()->create([
            'societe_id' => $autreSociete->id,
            'role'       => 'editeur',
        ]);

        $this->actingAs($this->admin)
            ->get(route('parametres.utilisateurs.show', $autreUser))
            ->assertForbidden();
    }
}
