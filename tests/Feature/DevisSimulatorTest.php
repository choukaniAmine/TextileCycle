<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Atelier;
use App\Models\Devis;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevisSimulatorTest extends TestCase
{
    use RefreshDatabase;

    protected Atelier $atelier;
    protected Service $service1;
    protected Service $service2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->atelier = Atelier::factory()->create([
            'nom' => 'Atelier Couture Test',
            'ville' => 'Tunis',
            'est_actif' => true,
        ]);

        $this->service1 = Service::factory()->create([
            'atelier_id' => $this->atelier->id,
            'nom' => 'Ourlet pantalon',
            'type_service' => 'Retouche',
            'tarif_estime' => 15.00,
            'disponible' => true,
        ]);

        $this->service2 = Service::factory()->create([
            'atelier_id' => $this->atelier->id,
            'nom' => 'Remplacement fermeture éclair',
            'type_service' => 'Reparation',
            'tarif_estime' => 25.00,
            'disponible' => true,
        ]);
    }

    public function test_atelier_show_page_displays_devis_simulator(): void
    {
        $response = $this->get(route('ateliers.show', $this->atelier));

        $response->assertStatus(200);
        $response->assertSee('Simulateur de Devis');
        $response->assertSee('Ourlet pantalon');
        $response->assertSee('Remplacement fermeture éclair');
    }

    public function test_can_generate_devis_via_ajax_with_eco_metrics(): void
    {
        $payload = [
            'nom_client' => 'Sarra Ben Ali',
            'email_client' => 'sarra@test.tn',
            'telephone_client' => '+216 98 123 456',
            'remarques' => 'Jean denim épais',
            'services' => [
                $this->service1->id => 2, // 2 * 15 = 30 DT
                $this->service2->id => 1, // 1 * 25 = 25 DT
            ],
        ];

        $response = $this->postJson(route('ateliers.devis.store', $this->atelier), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $devis = Devis::where('email_client', 'sarra@test.tn')->first();
        $this->assertNotNull($devis);
        $this->assertEquals(55.00, (float)$devis->montant_total);
        $this->assertEquals(3, $devis->items()->sum('quantite'));
        // 3 pieces * 6.5 kg = 19.5 kg CO2
        $this->assertEquals(19.5, (float)$devis->co2_economise);
        // 3 pieces * 2500 L = 7500 L eau
        $this->assertEquals(7500, $devis->eau_economisee);
        $this->assertStringStartsWith('DEV-', $devis->reference);
    }

    public function test_devis_validation_rejects_empty_selection(): void
    {
        $payload = [
            'nom_client' => 'Sarra Ben Ali',
            'email_client' => 'sarra@test.tn',
            'services' => [
                $this->service1->id => 0,
            ],
        ];

        $response = $this->postJson(route('ateliers.devis.store', $this->atelier), $payload);

        $response->assertStatus(422);
    }

    public function test_can_download_devis_as_pdf(): void
    {
        $devis = Devis::create([
            'atelier_id' => $this->atelier->id,
            'nom_client' => 'Client Test',
            'email_client' => 'client@test.tn',
            'montant_total' => 40.00,
            'co2_economise' => 13.0,
            'eau_economisee' => 5000,
            'duree_estimee_jours' => 3,
            'statut' => 'valide',
        ]);

        $devis->items()->create([
            'service_id' => $this->service1->id,
            'nom_service' => $this->service1->nom,
            'type_service' => $this->service1->type_service,
            'prix_unitaire' => 20.00,
            'quantite' => 2,
            'sous_total' => 40.00,
        ]);

        $response = $this->get(route('devis.download', $devis));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_can_stream_devis_pdf(): void
    {
        $devis = Devis::create([
            'atelier_id' => $this->atelier->id,
            'nom_client' => 'Client Test',
            'email_client' => 'client@test.tn',
            'montant_total' => 25.00,
            'co2_economise' => 6.5,
            'eau_economisee' => 2500,
            'duree_estimee_jours' => 2,
            'statut' => 'valide',
        ]);

        $devis->items()->create([
            'service_id' => $this->service2->id,
            'nom_service' => $this->service2->nom,
            'type_service' => $this->service2->type_service,
            'prix_unitaire' => 25.00,
            'quantite' => 1,
            'sous_total' => 25.00,
        ]);

        $response = $this->get(route('devis.stream', $devis));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
