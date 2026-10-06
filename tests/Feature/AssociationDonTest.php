<?php

namespace Tests\Feature;

use App\Enums\DonStatus;
use App\Enums\UserRole;
use App\Models\Association;
use App\Models\Don;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssociationDonTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->role(UserRole::Admin)->create();
    }

    public function test_association_has_many_dons(): void
    {
        $association = Association::factory()->has(Don::factory()->count(3))->create();

        $this->assertCount(3, $association->dons);
        $this->assertTrue($association->dons->first()->association->is($association));
    }

    public function test_non_admin_cannot_access_association_back_office(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/associations')->assertForbidden();
    }

    public function test_admin_crud_association(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/associations', ['name' => 'Espoir Tunisie', 'city' => 'Tunis', 'is_active' => 1])
            ->assertRedirect('/admin/associations');
        $association = Association::firstWhere('name', 'Espoir Tunisie');
        $this->assertNotNull($association);

        $this->actingAs($admin)->get("/admin/associations/{$association->id}/edit")->assertOk()->assertSee('Espoir Tunisie');
        $this->actingAs($admin)->put("/admin/associations/{$association->id}", ['name' => 'Espoir Tunisie 2'])
            ->assertRedirect('/admin/associations');
        $this->assertDatabaseHas('associations', ['id' => $association->id, 'name' => 'Espoir Tunisie 2', 'is_active' => false]);

        $this->actingAs($admin)->delete("/admin/associations/{$association->id}")->assertRedirect('/admin/associations');
        $this->assertDatabaseMissing('associations', ['id' => $association->id]);
    }

    public function test_association_validation_errors_and_old_input(): void
    {
        $admin = $this->admin();
        Association::factory()->create(['name' => 'Doublon']);

        $this->actingAs($admin)->from('/admin/associations/create')
            ->post('/admin/associations', ['name' => 'Doublon', 'email' => 'pas-un-email'])
            ->assertRedirect('/admin/associations/create')
            ->assertSessionHasErrors(['name', 'email']);
    }

    public function test_admin_creates_don_which_always_starts_en_attente(): void
    {
        $admin = $this->admin();
        $association = Association::factory()->create();

        // Même si l'admin tente d'imposer un statut, il est ignoré.
        $this->actingAs($admin)->post('/admin/dons', [
            'association_id' => $association->id, 'description' => '3 pantalons, 2 vestes', 'quantity' => 5,
            'donated_at' => now()->toDateString(), 'status' => 'livre',
        ])->assertRedirect('/admin/dons');

        $don = Don::firstWhere('association_id', $association->id);
        $this->assertSame(DonStatus::EnAttente, $don->status);
        $this->assertCount(1, $don->statusLogs);
    }

    public function test_admin_cannot_override_don_status(): void
    {
        $admin = $this->admin();
        $don = Don::factory()->status(DonStatus::EnAttente)->create();

        // Modification classique : le champ statut est ignoré.
        $this->actingAs($admin)->put("/admin/dons/{$don->id}", [
            'association_id' => $don->association_id, 'description' => 'Modifié', 'quantity' => 2,
            'donated_at' => now()->toDateString(), 'status' => 'livre',
        ])->assertRedirect();
        $this->assertSame(DonStatus::EnAttente, $don->fresh()->status);
        $this->assertSame('Modifié', $don->fresh()->description);

        // L'ancienne route de changement de statut n'existe plus.
        $this->actingAs($admin)->patch("/admin/dons/{$don->id}/statut", ['status' => 'livre'])->assertStatus(404);
        // L'admin n'a pas accès à l'espace association.
        $this->actingAs($admin)->patch("/espace-association/dons/{$don->id}/statut", ['status' => 'accepte'])->assertForbidden();
        $this->assertSame(DonStatus::EnAttente, $don->fresh()->status);
    }

    public function test_don_validation_rejects_bad_data(): void
    {
        $this->actingAs($this->admin())->post('/admin/dons', [
            'association_id' => 999, 'description' => '', 'quantity' => 0, 'donated_at' => now()->addDay()->toDateString(),
        ])->assertSessionHasErrors(['association_id', 'description', 'quantity', 'donated_at']);
    }

    private function managedAssociation(): array
    {
        $manager = User::factory()->role(UserRole::Association)->create();
        $association = Association::factory()->create(['manager_id' => $manager->id]);

        return [$manager, $association];
    }

    public function test_association_accepts_then_follows_don_to_delivery(): void
    {
        [$manager, $association] = $this->managedAssociation();
        $don = Don::factory()->for($association)->status(DonStatus::EnAttente)->create();

        foreach (['accepte', 'en_cours', 'livre'] as $step) {
            $this->actingAs($manager)->patch("/espace-association/dons/{$don->id}/statut", ['status' => $step])
                ->assertSessionHas('success');
            $this->assertSame($step, $don->fresh()->status->value);
        }

        // création + 3 étapes journalisées, la dernière par le compte de l'association
        $logs = $don->fresh()->statusLogs;
        $this->assertCount(4, $logs);
        $this->assertSame($manager->id, $logs->last()->user_id);
    }

    public function test_association_can_refuse_and_refused_don_is_final(): void
    {
        [$manager, $association] = $this->managedAssociation();
        $don = Don::factory()->for($association)->status(DonStatus::EnAttente)->create();

        $this->actingAs($manager)->patch("/espace-association/dons/{$don->id}/statut", ['status' => 'refuse'])->assertSessionHas('success');
        $this->actingAs($manager)->patch("/espace-association/dons/{$don->id}/statut", ['status' => 'accepte'])->assertSessionHas('error');
        $this->assertSame(DonStatus::Refuse, $don->fresh()->status);
    }

    public function test_association_cannot_skip_steps(): void
    {
        [$manager, $association] = $this->managedAssociation();
        $don = Don::factory()->for($association)->status(DonStatus::EnAttente)->create();

        $this->actingAs($manager)->patch("/espace-association/dons/{$don->id}/statut", ['status' => 'livre'])->assertSessionHas('error');
        $this->assertSame(DonStatus::EnAttente, $don->fresh()->status);
    }

    public function test_association_cannot_touch_another_associations_don(): void
    {
        [$manager] = $this->managedAssociation();
        $foreign = Don::factory()->status(DonStatus::EnAttente)->create();

        $this->actingAs($manager)->patch("/espace-association/dons/{$foreign->id}/statut", ['status' => 'accepte'])->assertForbidden();
        $this->assertSame(DonStatus::EnAttente, $foreign->fresh()->status);
    }

    public function test_donor_cannot_use_the_association_space(): void
    {
        $don = Don::factory()->status(DonStatus::EnAttente)->create();

        $this->actingAs(User::factory()->create())->get('/espace-association/dons')->assertForbidden();
        $this->actingAs(User::factory()->create())->patch("/espace-association/dons/{$don->id}/statut", ['status' => 'accepte'])->assertForbidden();
    }

    public function test_association_account_without_association_sees_a_notice(): void
    {
        $this->actingAs(User::factory()->role(UserRole::Association)->create())
            ->get('/espace-association/dons')->assertOk()->assertSee('Compte non rattaché');
    }

    public function test_received_dons_can_be_filtered_by_status(): void
    {
        [$manager, $association] = $this->managedAssociation();
        Don::factory()->for($association)->status(DonStatus::EnAttente)->create(['description' => 'ATTENTE-ZZ']);
        Don::factory()->for($association)->status(DonStatus::Livre)->create(['description' => 'LIVRE-ZZ']);

        $this->actingAs($manager)->get('/espace-association/dons?status=en_attente')
            ->assertOk()->assertSee('ATTENTE-ZZ')->assertDontSee('LIVRE-ZZ');
    }

    public function test_admin_assigns_manager_with_validation(): void
    {
        $admin = $this->admin();
        $manager = User::factory()->role(UserRole::Association)->create();
        $simpleUser = User::factory()->create();

        $this->actingAs($admin)->post('/admin/associations', ['name' => 'A1', 'manager_id' => $manager->id])->assertRedirect();
        $this->assertSame($manager->id, Association::firstWhere('name', 'A1')->manager_id);

        // un compte ne gère qu'une association, et doit avoir le rôle association
        $this->actingAs($admin)->post('/admin/associations', ['name' => 'A2', 'manager_id' => $manager->id])->assertSessionHasErrors('manager_id');
        $this->actingAs($admin)->post('/admin/associations', ['name' => 'A3', 'manager_id' => $simpleUser->id])->assertSessionHasErrors('manager_id');
    }

    public function test_dons_can_be_filtered_by_status(): void
    {
        $association = Association::factory()->create();
        Don::factory()->for($association)->status(DonStatus::EnAttente)->create(['description' => 'DON-ATTENTE-X']);
        Don::factory()->for($association)->status(DonStatus::Livre)->create(['description' => 'DON-LIVRE-X']);

        $this->actingAs($this->admin())->get('/admin/dons?status=livre')
            ->assertOk()->assertSee('DON-LIVRE-X')->assertDontSee('DON-ATTENTE-X');
    }

    public function test_user_can_make_and_follow_own_don_but_not_others(): void
    {
        $user = User::factory()->create();
        $association = Association::factory()->create();

        $this->actingAs($user)->post('/mes-dons', [
            'association_id' => $association->id, 'description' => '2 manteaux', 'quantity' => 2, 'donated_at' => now()->toDateString(),
        ])->assertRedirect();

        $don = $user->dons()->first();
        $this->assertSame(DonStatus::EnAttente, $don->status);
        $this->actingAs($user)->get('/mes-dons')->assertOk()->assertSee('2 manteaux');
        $this->actingAs($user)->get("/mes-dons/{$don->id}")->assertOk();

        $this->actingAs(User::factory()->create())->get("/mes-dons/{$don->id}")->assertForbidden();
    }

    public function test_user_cannot_cancel_processed_don(): void
    {
        $user = User::factory()->create();
        $don = Don::factory()->for($user, 'donor')->status(DonStatus::Livre)->create();

        $this->actingAs($user)->delete("/mes-dons/{$don->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('dons', ['id' => $don->id]);
    }

    public function test_public_association_pages(): void
    {
        $association = Association::factory()->create(['name' => 'Visible Asso']);
        $hidden = Association::factory()->inactive()->create();

        $this->get('/associations')->assertOk()->assertSee('Visible Asso');
        $this->get("/associations/{$association->id}")->assertOk();
        $this->get("/associations/{$hidden->id}")->assertNotFound();
    }

    public function test_every_module_page_renders_on_seeded_data(): void
    {
        $this->seed();
        $admin = User::firstWhere('email', 'admin@refashion.test');
        $association = Association::firstWhere('name', 'Espoir Tunisie');
        $don = $association->dons()->first();

        foreach ([
            '/admin/associations', '/admin/associations/create', "/admin/associations/{$association->id}",
            "/admin/associations/{$association->id}/edit", '/admin/dons', '/admin/dons?status=en_attente&q=pantalons',
            '/admin/dons/create', "/admin/dons/create?association={$association->id}", "/admin/dons/{$don->id}", "/admin/dons/{$don->id}/edit",
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $donor = $don->donor;
        foreach (['/mes-dons', '/mes-dons?status=en_attente', '/mes-dons/nouveau', "/mes-dons/{$don->id}"] as $url) {
            $this->actingAs($donor)->get($url)->assertOk();
        }
        $this->get('/associations?q=Espoir')->assertOk()->assertSee('Espoir Tunisie');

        // L'association de démonstration accepte ses dons depuis son espace.
        $manager = User::firstWhere('email', 'association@refashion.test');
        $this->assertSame($association->id, $manager->managedAssociation->id);
        $this->actingAs($manager)->get('/espace-association/dons')->assertOk()->assertSee('3 pantalons, 2 vestes');
    }

    public function test_seeders_create_related_data(): void
    {
        $this->seed();

        $espoir = Association::firstWhere('name', 'Espoir Tunisie');
        $this->assertNotNull($espoir);
        $this->assertSame('3 pantalons, 2 vestes', $espoir->dons->first()->description);
        $this->assertGreaterThan(5, Don::count());
    }
}
