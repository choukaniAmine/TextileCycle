<?php

namespace Tests\Feature;

use App\Enums\Statut;
use App\Enums\TypeDemande;
use App\Enums\UserRole;
use App\Models\Demande;
use App\Models\User;
use App\Notifications\DemandeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DemandeTest extends TestCase
{
    use RefreshDatabase;

    private function atelier(): User
    {
        return User::factory()->role(UserRole::Atelier)->create(['is_active' => true]);
    }

    private function client(): User
    {
        // On ajoute le rôle particulier pour passer le middleware 'role:particulier,association'
        return User::factory()->role(UserRole::Particulier)->create();
    }

    private function payload(array $overrides = []): array
    {
        $type = TypeDemande::cases()[0]->value;

        return array_merge([
            'titre' => 'Retouche pantalon',
            'type' => $type,
            'vetement' => 'Pantalon',
            'description' => 'Ourlet à refaire sur un pantalon en laine.',
        ], $overrides);
    }

    public function test_user_can_create_a_demande(): void
    {
        Notification::fake();
        $user = $this->client();
        $atelier = $this->atelier();

        // CORRECTION : /mes-demandes au lieu de /demandes
        $this->actingAs($user)
            ->post('/mes-demandes', $this->payload(['urgent' => 1]))
            ->assertRedirect();

        $demande = $user->demandes()->first();
        $this->assertNotNull($demande);
        $this->assertSame('Retouche pantalon', $demande->titre);
        $this->assertTrue((bool) $demande->urgent);
        $this->assertSame(Statut::EnAttente, $demande->statut);

        Notification::assertSentTo($atelier, DemandeNotification::class);
    }

    public function test_demande_validation_rejects_bad_data(): void
    {
        $this->actingAs($this->client())
            // CORRECTION : /mes-demandes
            ->post('/mes-demandes', [
                'titre' => '',
                'type' => 'inconnu',
                'vetement' => '',
                'description' => 'court',
                'date_souhaitee' => now()->subDay()->toDateString(),
                'niveau_difficulte' => 99,
            ])
            ->assertSessionHasErrors([
                'titre', 'type', 'vetement', 'description', 'date_souhaitee', 'niveau_difficulte',
            ]);
    }

    public function test_user_can_view_own_demande_but_not_others(): void
    {
        $user = $this->client();
        $demande = Demande::factory()->for($user)->create();

        // CORRECTION : /mes-demandes
        $this->actingAs($user)->get("/mes-demandes/{$demande->id}")->assertOk();

        $other = $this->client();
        $this->actingAs($other)->get("/mes-demandes/{$demande->id}")->assertForbidden();
    }

    public function test_index_lists_only_own_demandes_with_status_filter(): void
    {
        $user = $this->client();
        Demande::factory()->for($user)->create(['titre' => 'MA-DEMANDE-EN-ATTENTE', 'statut' => Statut::EnAttente]);
        Demande::factory()->for($user)->create(['titre' => 'MA-DEMANDE-TERMINEE', 'statut' => Statut::Terminee]);
        Demande::factory()->create(['titre' => 'DEMANDE-DU-VOISIN', 'statut' => Statut::EnAttente]);

        // CORRECTION : /mes-demandes
        $this->actingAs($user)->get('/mes-demandes')
            ->assertOk()
            ->assertSee('MA-DEMANDE-EN-ATTENTE')
            ->assertSee('MA-DEMANDE-TERMINEE')
            ->assertDontSee('DEMANDE-DU-VOISIN');

        $this->actingAs($user)->get('/mes-demandes?statut=' . Statut::EnAttente->value)
            ->assertOk()
            ->assertSee('MA-DEMANDE-EN-ATTENTE')
            ->assertDontSee('MA-DEMANDE-TERMINEE');
    }

    public function test_user_can_update_own_pending_demande(): void
    {
        Notification::fake();
        $user = $this->client();
        $demande = Demande::factory()->for($user)->create(['statut' => Statut::EnAttente]);

        // CORRECTION : /mes-demandes
        $this->actingAs($user)
            ->put("/mes-demandes/{$demande->id}", $this->payload(['titre' => 'Titre modifié']))
            ->assertRedirect(route('demandes.show', $demande));

        $this->assertSame('Titre modifié', $demande->fresh()->titre);
    }

    public function test_user_cannot_update_a_demande_already_taken(): void
    {
        $user = $this->client();
        $demande = Demande::factory()->for($user)->create(['statut' => Statut::EnCours]);

        // CORRECTION : /mes-demandes
        $this->actingAs($user)
            ->put("/mes-demandes/{$demande->id}", $this->payload(['titre' => 'Pirate']))
            ->assertForbidden();

        $this->assertNotSame('Pirate', $demande->fresh()->titre);
    }

    public function test_edit_redirects_when_demande_cannot_be_modified(): void
    {
        $user = $this->client();
        $demande = Demande::factory()->for($user)->create(['statut' => Statut::EnCours]);

        // CORRECTION : /mes-demandes
        $this->actingAs($user)
            ->get("/mes-demandes/{$demande->id}/edit")
            ->assertRedirect(route('demandes.show', $demande))
            ->assertSessionHas('error');
    }

    public function test_user_can_cancel_pending_demande(): void
    {
        $user = $this->client();
        $demande = Demande::factory()->for($user)->create(['statut' => Statut::EnAttente]);

        // CORRECTION : /mes-demandes/{id}/annuler
        $this->actingAs($user)->patch("/mes-demandes/{$demande->id}/annuler")
            ->assertRedirect(route('demandes.index'))
            ->assertSessionHas('success');

        $this->assertSame(Statut::Annulee, $demande->fresh()->statut);
    }

    public function test_user_cannot_cancel_processed_demande(): void
    {
        $user = $this->client();
        $demande = Demande::factory()->for($user)->create(['statut' => Statut::EnCours]);

        // CORRECTION : /mes-demandes/{id}/annuler
        $this->actingAs($user)->patch("/mes-demandes/{$demande->id}/annuler")->assertForbidden();
        $this->assertSame(Statut::EnCours, $demande->fresh()->statut);
    }

    public function test_user_cannot_delete_non_deletable_demande(): void
    {
        $user = $this->client();
        $demande = Demande::factory()->for($user)->create(['statut' => Statut::Terminee]);

        // CORRECTION : /mes-demandes
        $this->actingAs($user)->delete("/mes-demandes/{$demande->id}")->assertForbidden();
        $this->assertDatabaseHas('demandes', ['id' => $demande->id]);
    }

    public function test_user_cannot_cancel_or_delete_foreign_demande(): void
    {
        $intruder = $this->client();
        $demande = Demande::factory()->create(['statut' => Statut::EnAttente]);

        // CORRECTION : /mes-demandes
        $this->actingAs($intruder)->patch("/mes-demandes/{$demande->id}/annuler")->assertForbidden();
        $this->actingAs($intruder)->delete("/mes-demandes/{$demande->id}")->assertForbidden();
        $this->actingAs($intruder)->get("/mes-demandes/{$demande->id}")->assertForbidden();
        $this->assertSame(Statut::EnAttente, $demande->fresh()->statut);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        // CORRECTION : /mes-demandes
        $this->get('/mes-demandes')->assertRedirect('/login');
        $this->post('/mes-demandes', $this->payload())->assertRedirect('/login');
    }

    public function test_ai_estimation_is_stored_on_create_and_recalculated_on_update(): void
    {
        Notification::fake();
        $user = $this->client();

        // CORRECTION : /mes-demandes
        $this->actingAs($user)->post('/mes-demandes', $this->payload([
            'categorie_vetement' => array_key_first(config('repair.categories')) ?? null,
            'type_intervention' => array_key_first(config('repair.interventions')) ?? null,
            'niveau_difficulte' => 2,
        ]))->assertRedirect();

        /** @var Demande $demande */
        $demande = $user->demandes()->first();
        $this->assertNotNull($demande);

        $this->assertTrue(
            $demande->duree_estimee_min !== null || $demande->estimation !== null,
            "L'estimation IA devrait renseigner `duree_estimee_min` ou `estimation` sur la demande."
        );

        // CORRECTION : /mes-demandes
        $this->actingAs($user)->put("/mes-demandes/{$demande->id}", $this->payload([
            'titre' => 'Retouche maj',
            'niveau_difficulte' => 3,
        ]))->assertRedirect();

        $this->assertSame('Retouche maj', $demande->fresh()->titre);
    }

    public function test_create_page_renders(): void
    {
        // CORRECTION : /mes-demandes/create
        $this->actingAs($this->client())->get('/mes-demandes/create')->assertOk();
    }
}
