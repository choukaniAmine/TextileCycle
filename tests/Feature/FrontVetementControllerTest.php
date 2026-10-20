<?php

namespace Tests\Feature;

use App\Enums\EtatVetement;
use App\Enums\StatutDemande;
use App\Enums\StatutVetement;
use App\Enums\TypeVetement;
use App\Models\Categorie;
use App\Models\DemandeDon;
use App\Models\User;
use App\Models\Vetement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontVetementControllerTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Aides pour fabriquer les données ----------

    private function categorie(string $nom = 'Pantalon'): Categorie
    {
        return Categorie::forceCreate(['nom' => $nom]);
    }

    private function vetement(array $attributs = []): Vetement
    {
        return Vetement::create(array_merge([
            'nom' => 'Jean bleu',
            'description' => 'Jean en bon état',
            'taille' => 'M',
            'etat' => EtatVetement::cases()[0],
            'type' => TypeVetement::cases()[0],
            'statut' => StatutVetement::Disponible,
            'categorie_id' => $this->categorie('Cat '.uniqid())->id,
            'user_id' => User::factory()->create()->id,
        ], $attributs));
    }

    // ---------- index() ----------

    public function test_la_liste_est_accessible_sans_connexion(): void
    {
        $this->get(route('vetements.index'))
            ->assertOk()
            ->assertViewIs('front.vetements.index')
            ->assertViewHasAll(['vetements', 'categories', 'etats']);
    }

    public function test_la_liste_affiche_uniquement_les_vetements_disponibles(): void
    {
        $autreStatut = collect(StatutVetement::cases())
            ->first(fn ($s) => $s !== StatutVetement::Disponible);

        $dispo = $this->vetement(['nom' => 'Visible']);
        $this->vetement(['nom' => 'Cache', 'statut' => $autreStatut]);

        $this->get(route('vetements.index'))
            ->assertOk()
            ->assertSee('Visible')
            ->assertDontSee('Cache')
            ->assertViewHas('vetements', fn ($page) => $page->total() === 1
                && $page->first()->is($dispo));
    }

    public function test_le_filtre_par_categorie_fonctionne(): void
    {
        $pantalon = $this->categorie('Pantalon');
        $robe = $this->categorie('Robe');

        $this->vetement(['nom' => 'Jean', 'categorie_id' => $pantalon->id]);
        $this->vetement(['nom' => 'Robe rouge', 'categorie_id' => $robe->id]);

        $this->get(route('vetements.index', ['categorie_id' => $robe->id]))
            ->assertOk()
            ->assertViewHas('vetements', fn ($page) => $page->total() === 1
                && $page->first()->nom === 'Robe rouge');
    }

    public function test_le_filtre_par_etat_fonctionne(): void
    {
        $etats = EtatVetement::cases();
        $this->vetement(['nom' => 'Etat A', 'etat' => $etats[0]]);
        $this->vetement(['nom' => 'Etat B', 'etat' => $etats[1]]);

        $this->get(route('vetements.index', ['etat' => $etats[1]->value]))
            ->assertOk()
            ->assertViewHas('vetements', fn ($page) => $page->total() === 1
                && $page->first()->nom === 'Etat B');
    }

    public function test_la_liste_est_paginee_par_9(): void
    {
        $categorie = $this->categorie();
        $user = User::factory()->create();

        for ($i = 1; $i <= 12; $i++) {
            $this->vetement(['nom' => "V{$i}", 'categorie_id' => $categorie->id, 'user_id' => $user->id]);
        }

        $this->get(route('vetements.index'))
            ->assertViewHas('vetements', fn ($page) => $page->count() === 9 && $page->total() === 12);
    }

    // ---------- show() ----------

    public function test_la_fiche_affiche_le_vetement(): void
    {
        $vetement = $this->vetement(['nom' => 'Veste en cuir']);

        $this->get(route('vetements.show', $vetement))
            ->assertOk()
            ->assertViewIs('front.vetements.show')
            ->assertSee('Veste en cuir')
            ->assertViewHas('vetement', fn ($v) => $v->is($vetement)
                && $v->relationLoaded('categorie')
                && $v->relationLoaded('user'));
    }

    public function test_la_fiche_inconnue_renvoie_404(): void
    {
        $this->get(route('vetements.show', 99999))->assertNotFound();
    }

    public function test_visiteur_sans_connexion_na_pas_de_demande_en_cours(): void
    {
        $vetement = $this->vetement();

        $this->get(route('vetements.show', $vetement))
            ->assertViewHas('demandeEnCours', false);
    }

    public function test_connecte_sans_demande_na_pas_de_demande_en_cours(): void
    {
        $vetement = $this->vetement();
        $demandeur = User::factory()->create();

        $this->actingAs($demandeur)
            ->get(route('vetements.show', $vetement))
            ->assertOk()
            ->assertViewHas('demandeEnCours', false);
    }

    public function test_demande_en_attente_du_demandeur_est_detectee(): void
    {
        $vetement = $this->vetement();
        $demandeur = User::factory()->create();

        DemandeDon::forceCreate([
            'vetement_id' => $vetement->id,
            'demandeur_id' => $demandeur->id,
            'statut' => StatutDemande::EnAttente,
        ]);

        $this->actingAs($demandeur)
            ->get(route('vetements.show', $vetement))
            ->assertViewHas('demandeEnCours', true);
    }

    public function test_demande_dun_autre_utilisateur_nest_pas_comptee(): void
    {
        $vetement = $this->vetement();
        $autre = User::factory()->create();
        $moi = User::factory()->create();

        DemandeDon::forceCreate([
            'vetement_id' => $vetement->id,
            'demandeur_id' => $autre->id,
            'statut' => StatutDemande::EnAttente,
        ]);

        $this->actingAs($moi)
            ->get(route('vetements.show', $vetement))
            ->assertViewHas('demandeEnCours', false);
    }

    public function test_demande_deja_traitee_nest_pas_comptee(): void
    {
        $vetement = $this->vetement();
        $demandeur = User::factory()->create();
        $autreStatut = collect(StatutDemande::cases())
            ->first(fn ($s) => $s !== StatutDemande::EnAttente);

        DemandeDon::forceCreate([
            'vetement_id' => $vetement->id,
            'demandeur_id' => $demandeur->id,
            'statut' => $autreStatut,
        ]);

        $this->actingAs($demandeur)
            ->get(route('vetements.show', $vetement))
            ->assertViewHas('demandeEnCours', false);
    }
}