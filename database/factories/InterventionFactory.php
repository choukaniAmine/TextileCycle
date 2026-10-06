<?php

namespace Database\Factories;

use App\Enums\Statut;
use App\Models\Demande;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterventionFactory extends Factory
{
    public function definition(): array
    {
        $statut = fake()->randomElement([Statut::EnAttente, Statut::EnCours, Statut::Terminee]);
        $debut = $statut === Statut::EnAttente ? null : now()->subDays(rand(2, 12));

        return [
            'demande_id' => Demande::factory(),
            'titre' => fake()->randomElement([
                'Remplacement de la fermeture', 'Recoudre la déchirure', 'Changement des boutons',
                'Reprise de l’ourlet', 'Découpe et assemblage', 'Teinture et finitions',
            ]),
            'description' => fake()->sentence(10),
            'statut' => $statut,
            'cout_estime' => fake()->randomFloat(2, 5, 80),
            'date_debut' => $debut,
            'date_fin' => $statut === Statut::Terminee ? now()->subDays(rand(0, 1)) : null,
        ];
    }
}
