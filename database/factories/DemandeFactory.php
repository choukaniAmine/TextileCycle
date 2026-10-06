<?php

namespace Database\Factories;

use App\Enums\Statut;
use App\Enums\TypeDemande;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DemandeFactory extends Factory
{
    public function definition(): array
    {
        $cas = [
            ['Réparer une veste déchirée', 'Veste en jean', 'reparation'],
            ['Fermeture éclair cassée', 'Manteau d’hiver', 'reparation'],
            ['Raccourcir un pantalon', 'Pantalon en lin', 'reparation'],
            ['Recoudre une doublure', 'Blazer', 'reparation'],
            ['Transformer une robe en jupe', 'Robe longue', 'transformation'],
            ['Jean en sac à dos', 'Vieux jean', 'transformation'],
            ['Chemise en tote bag', 'Chemise en coton', 'transformation'],
        ];
        [$titre, $vetement, $type] = fake()->randomElement($cas);

        return [
            'user_id' => User::factory(),
            'atelier_id' => null,
            'titre' => $titre,
            'type' => $type,
            'vetement' => $vetement,
            'description' => fake()->paragraph(2),
            'urgent' => fake()->boolean(20),
            'date_souhaitee' => fake()->optional(0.6)->dateTimeBetween('+3 days', '+40 days'),
            'statut' => Statut::EnAttente,
        ];
    }
}
