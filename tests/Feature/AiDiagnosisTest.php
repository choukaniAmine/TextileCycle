<?php

namespace Tests\Feature;

use App\Models\Atelier;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiDiagnosisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $atelier = Atelier::factory()->create([
            'nom' => 'Atelier Test Tunis',
            'ville' => 'Tunis',
        ]);

        Service::create([
            'atelier_id' => $atelier->id,
            'nom' => 'Réparation fermeture éclair et accrocs',
            'type_service' => 'Réparation',
            'description' => 'Changement de zip et reprise couture pour pantalons et vestes',
            'tarif_estime' => 15.00,
            'duree_estimee' => '2 jours',
            'disponible' => true,
        ]);
    }

    public function test_ai_diagnosis_validates_image(): void
    {
        $response = $this->postJson(route('ateliers.ai.diagnose'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photo']);
    }

    public function test_ai_diagnosis_rejects_non_image_file(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson(route('ateliers.ai.diagnose'), [
            'photo' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photo']);
    }

    public function test_ai_diagnosis_returns_successful_analysis(): void
    {
        // 1x1 transparent/dummy jpeg bytes
        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $file = UploadedFile::fake()->createWithContent('jean_abime.jpg', $content);

        $response = $this->postJson(route('ateliers.ai.diagnose'), [
            'photo' => $file,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'garment_type',
                    'material_detected',
                    'damage_type',
                    'severity',
                    'repairability_score',
                    'repair_advice',
                    'matching_services',
                    'matching_ateliers',
                ]
            ]);
    }

    public function test_ai_diagnosis_handles_non_garment_or_undamaged_rejection(): void
    {
        // Mocking Http to simulate Gemini rejecting a non-garment image
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'is_valid_garment_damage' => false,
                                        'rejection_reason' => 'not_a_garment',
                                        'rejection_message' => "L'image fournie ne semble pas être un vêtement ou un article textile.",
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $file = UploadedFile::fake()->createWithContent('paysage.jpg', $content);

        $response = $this->postJson(route('ateliers.ai.diagnose'), [
            'photo' => $file,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_valid_garment_damage', false)
            ->assertJsonPath('data.rejection_reason', 'not_a_garment');
    }
}
