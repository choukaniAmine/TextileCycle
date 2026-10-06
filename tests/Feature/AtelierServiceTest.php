<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Atelier;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtelierServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_front_ateliers_catalogue(): void
    {
        $atelier = Atelier::factory()->create(['est_actif' => true]);

        $response = $this->get(route('ateliers.index'));
        $response->assertStatus(200);
        $response->assertSee($atelier->nom);
    }

    public function test_can_view_front_atelier_details_with_services(): void
    {
        $atelier = Atelier::factory()->create(['est_actif' => true]);
        $service = Service::factory()->create([
            'atelier_id' => $atelier->id,
            'nom' => 'Ourlet Pantalon',
            'disponible' => true
        ]);

        $response = $this->get(route('ateliers.show', $atelier));
        $response->assertStatus(200);
        $response->assertSee($atelier->nom);
        $response->assertSee('Ourlet Pantalon');
    }

    public function test_admin_can_create_atelier_with_validation(): void
    {
        $admin = User::factory()->role(UserRole::Admin)->create();

        $data = [
            'nom' => 'Atelier Eco Test',
            'description' => 'Un atelier éco-responsable pour la réparation.',
            'adresse' => '10 Rue de la Liberté',
            'ville' => 'Tunis',
            'code_postal' => '1000',
            'telephone' => '+216 20 123 456',
            'email' => 'eco@test.tn',
            'horaires' => '09h - 18h',
            'est_actif' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.ateliers.store'), $data);
        $response->assertRedirect(route('admin.ateliers.index'));
        $this->assertDatabaseHas('ateliers', ['nom' => 'Atelier Eco Test']);
    }

    public function test_admin_can_create_service_for_atelier(): void
    {
        $admin = User::factory()->role(UserRole::Admin)->create();
        $atelier = Atelier::factory()->create();

        $data = [
            'atelier_id' => $atelier->id,
            'nom' => 'Transformation Chemise',
            'type_service' => 'Transformation',
            'description' => 'Transformation en sans manche',
            'tarif_estime' => 35.50,
            'duree_estimee' => '48h',
            'disponible' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.services.store'), $data);
        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', [
            'nom' => 'Transformation Chemise',
            'atelier_id' => $atelier->id,
        ]);
    }

    public function test_admin_can_update_atelier(): void
    {
        $admin = User::factory()->role(UserRole::Admin)->create();
        $atelier = Atelier::factory()->create(['nom' => 'Ancien Nom Atelier']);

        $response = $this->actingAs($admin)->put(route('admin.ateliers.update', $atelier), [
            'nom' => 'Nouveau Nom Atelier',
            'adresse' => $atelier->adresse,
            'ville' => $atelier->ville,
            'telephone' => $atelier->telephone,
        ]);

        $response->assertRedirect(route('admin.ateliers.index'));
        $this->assertDatabaseHas('ateliers', ['nom' => 'Nouveau Nom Atelier']);
    }

    public function test_admin_can_delete_atelier_and_cascade_services(): void
    {
        $admin = User::factory()->role(UserRole::Admin)->create();
        $atelier = Atelier::factory()->create();
        $service = Service::factory()->create(['atelier_id' => $atelier->id]);

        $response = $this->actingAs($admin)->delete(route('admin.ateliers.destroy', $atelier));
        $response->assertRedirect(route('admin.ateliers.index'));

        $this->assertDatabaseMissing('ateliers', ['id' => $atelier->id]);
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_authenticated_user_can_leave_review_on_atelier(): void
    {
        $user = User::factory()->create();
        $atelier = Atelier::factory()->create(['est_actif' => true]);

        $response = $this->actingAs($user)->post(route('ateliers.avis.store', $atelier), [
            'note' => 5,
            'commentaire' => 'Atelier exceptionnel et travail soigné !',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('avis', [
            'atelier_id' => $atelier->id,
            'user_id' => $user->id,
            'note' => 5,
            'commentaire' => 'Atelier exceptionnel et travail soigné !',
        ]);
        $this->assertEquals(5.0, $atelier->moyenneNote());
        $this->assertEquals(1, $atelier->nombreAvis());
    }

    public function test_user_can_update_existing_review(): void
    {
        $user = User::factory()->create();
        $atelier = Atelier::factory()->create(['est_actif' => true]);

        $this->actingAs($user)->post(route('ateliers.avis.store', $atelier), [
            'note' => 3,
            'commentaire' => 'Moyen',
        ]);

        // Mise à jour de l'avis
        $this->actingAs($user)->post(route('ateliers.avis.store', $atelier), [
            'note' => 5,
            'commentaire' => 'Finalement parfait !',
        ]);

        $this->assertDatabaseCount('avis', 1);
        $this->assertDatabaseHas('avis', [
            'atelier_id' => $atelier->id,
            'user_id' => $user->id,
            'note' => 5,
            'commentaire' => 'Finalement parfait !',
        ]);
    }

    public function test_user_can_delete_their_own_review(): void
    {
        $user = User::factory()->create();
        $atelier = Atelier::factory()->create(['est_actif' => true]);

        $this->actingAs($user)->post(route('ateliers.avis.store', $atelier), [
            'note' => 4,
            'commentaire' => 'Bien',
        ]);

        $avis = \App\Models\Avis::where('atelier_id', $atelier->id)->first();

        $response = $this->actingAs($user)->delete(route('avis.destroy', $avis));
        $response->assertRedirect(route('ateliers.show', $atelier));
        $this->assertDatabaseMissing('avis', ['id' => $avis->id]);
    }

    public function test_user_can_delete_review_via_atelier_route(): void
    {
        $user = User::factory()->create();
        $atelier = Atelier::factory()->create(['est_actif' => true]);

        $this->actingAs($user)->post(route('ateliers.avis.store', $atelier), [
            'note' => 4,
            'commentaire' => 'Bien',
        ]);

        $response = $this->actingAs($user)->delete(route('ateliers.avis.destroy', $atelier));
        $response->assertRedirect(route('ateliers.show', $atelier));
        $this->assertDatabaseMissing('avis', ['atelier_id' => $atelier->id, 'user_id' => $user->id]);
    }

    public function test_ajax_filter_returns_json_response(): void
    {
        Atelier::factory()->create(['nom' => 'Atelier Unique Eco', 'est_actif' => true, 'ville' => 'Tunis']);

        $response = $this->getJson(route('ateliers.index', ['search' => 'Unique']));
        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'total', 'pagination']);
        $this->assertEquals(1, $response->json('total'));
    }
}

