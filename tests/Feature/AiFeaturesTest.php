<?php

namespace Tests\Feature;

use App\Enums\Statut;
use App\Enums\UserRole;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private const FEATURES = [
        'categorie_vetement' => 'jean', 'type_intervention' => 'dechirure',
        'type_demande' => 'reparation', 'niveau_difficulte' => 2, 'description' => 'Déchirure au genou',
    ];

    private function grokResponse(array $analyse): array
    {
        return ['output' => [['type' => 'message', 'role' => 'assistant',
            'content' => [['type' => 'output_text', 'text' => json_encode($analyse)]]]]];
    }

    private function analyse(array $override = []): array
    {
        return array_merge([
            'type_vetement' => 'Jean', 'categorie_vetement' => 'jean', 'probleme' => 'Déchirure au genou',
            'type_demande' => 'reparation', 'type_intervention' => 'dechirure', 'niveau_difficulte' => 2,
            'titre_suggere' => 'Réparer un jean déchiré', 'description_suggeree' => 'Mon jean est déchiré au genou.',
            'solutions' => [['titre' => 'Pièce thermocollante', 'explication' => 'Discret et peu coûteux.',
                'avantages' => ['Discret', 'Rapide'], 'cout_relatif' => 'faible', 'difficulte' => 'facile']],
            'informations_manquantes' => ['Taille de la déchirure ?'],
        ], $override);
    }

    // ---------- IA n°1 : estimation locale ----------

    public function test_estimation_degrades_gracefully_when_local_service_is_offline(): void
    {
        Http::fake(fn () => throw new ConnectionException('refused'));

        $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/estimation', self::FEATURES)
            ->assertOk()->assertJson(['disponible' => false, 'raison' => 'service_hors_ligne']);
    }

    public function test_estimation_returns_service_prediction(): void
    {
        Http::fake(['127.0.0.1:8001/*' => Http::response([
            'disponible' => true, 'mode' => 'modele', 'minutes' => 90, 'min' => 60, 'max' => 120,
            'confiance' => 'moyenne', 'n_echantillons' => 40,
        ])]);

        $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/estimation', self::FEATURES)
            ->assertOk()->assertJson(['disponible' => true, 'minutes' => 90, 'mode' => 'modele']);
    }

    public function test_incoherent_service_answer_is_rejected(): void
    {
        Http::fake(['127.0.0.1:8001/*' => Http::response(['disponible' => true, 'minutes' => 0])]);

        $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/estimation', self::FEATURES)
            ->assertOk()->assertJson(['disponible' => false, 'raison' => 'reponse_invalide']);
    }

    public function test_demande_stores_estimation_on_creation(): void
    {
        Http::fake(['127.0.0.1:8001/*' => Http::response([
            'disponible' => true, 'mode' => 'mediane', 'minutes' => 75, 'min' => 60, 'max' => 90, 'confiance' => 'faible', 'n_echantillons' => 4,
        ])]);

        $this->actingAs(User::factory()->create())->post('/mes-demandes', [
            'titre' => 'Jean déchiré', 'type' => 'reparation', 'vetement' => 'Jean bleu',
            'description' => 'Déchirure au niveau du genou.', ...self::FEATURES,
        ])->assertRedirect();

        $this->assertDatabaseHas('demandes', ['categorie_vetement' => 'jean', 'duree_estimee_min' => 75]);
    }

    public function test_atelier_confirms_real_duration(): void
    {
        $atelier = User::factory()->role(UserRole::Atelier)->create();
        $demande = Demande::factory()->create(['atelier_id' => $atelier->id, 'statut' => Statut::EnCours, 'duree_estimee_min' => 90]);

        $this->actingAs($atelier)->patch("/atelier/travaux/{$demande->id}/duree", ['heures' => 1, 'minutes' => 45])->assertRedirect();

        $this->assertDatabaseHas('demandes', ['id' => $demande->id, 'duree_reelle_min' => 105, 'duree_reelle_source' => 'atelier']);
    }

    public function test_other_atelier_cannot_set_duration(): void
    {
        $owner = User::factory()->role(UserRole::Atelier)->create();
        $other = User::factory()->role(UserRole::Atelier)->create();
        $demande = Demande::factory()->create(['atelier_id' => $owner->id, 'statut' => Statut::EnCours]);

        $this->actingAs($other)->patch("/atelier/travaux/{$demande->id}/duree", ['heures' => 1, 'minutes' => 0])->assertForbidden();
    }

    // ---------- IA n°2 : assistant Grok ----------

    public function test_assistant_returns_validated_analysis_and_never_creates_a_demande(): void
    {
        config(['ai.grok.key' => 'test-secret-key']);
        Http::fake(['api.x.ai/*' => Http::response($this->grokResponse($this->analyse([
            'categorie_vetement' => 'categorie_inventee', // doit être neutralisée
        ])))]);

        $response = $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/assistant', ['description' => 'Mon jean est déchiré au genou, je veux le réparer.']);

        $response->assertOk()->assertJson(['ok' => true, 'analyse' => ['type_intervention' => 'dechirure', 'categorie_vetement' => null]]);
        $this->assertDatabaseCount('demandes', 0);
        $this->assertStringNotContainsString('test-secret-key', $response->getContent());

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/responses')
            && $r->hasHeader('Authorization', 'Bearer test-secret-key')
            && $r['model'] === config('ai.grok.model')
            && $r['store'] === false);
    }

    public function test_assistant_without_api_key_returns_503(): void
    {
        config(['ai.grok.key' => null]);
        Http::fake();

        $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/assistant', ['description' => 'Mon jean est déchiré au genou.'])
            ->assertStatus(503)->assertJson(['ok' => false]);

        Http::assertNothingSent();
    }

    public function test_assistant_handles_xai_rate_limit(): void
    {
        config(['ai.grok.key' => 'k']);
        Http::fake(['api.x.ai/*' => Http::response(['error' => ['message' => 'slow down']], 429, ['Retry-After' => '12'])]);

        $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/assistant', ['description' => 'Mon jean est déchiré au genou.'])
            ->assertStatus(429)->assertJson(['ok' => false]);
    }

    public function test_assistant_rejects_non_json_answer(): void
    {
        config(['ai.grok.key' => 'k']);
        Http::fake(['api.x.ai/*' => Http::response(['output' => [['type' => 'message', 'content' => [['type' => 'output_text', 'text' => 'pas du json']]]]])]);

        $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/assistant', ['description' => 'Mon jean est déchiré au genou.'])
            ->assertStatus(502)->assertJson(['ok' => false]);
    }

    public function test_assistant_validates_description_length(): void
    {
        config(['ai.grok.key' => 'k']);

        $this->actingAs(User::factory()->create())
            ->postJson('/mes-demandes/assistant', ['description' => 'trop court'])
            ->assertStatus(422);
    }
}
