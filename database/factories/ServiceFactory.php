<?php

namespace Database\Factories;

use App\Models\Atelier;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $services = [
            'Reparation' => [
                'Réparation fermeture éclair blouson / jean',
                'Ravaudage accroc et déchirure veste',
                'Renfort coutures et doublures',
                'Remplacement boutons et boutons-pression',
            ],
            'Retouche' => [
                'Ourlet invisible pantalon / jupe',
                'Ajustement taille pantalon / robe',
                'Raccourcissement manches chemise',
                'Reprise cintrage veste de costume',
            ],
            'Transformation' => [
                'Transformation jean usé en sac cabas',
                'Conversion robe longue en ensemble 2 pièces',
                'Ajout empiècements cuir / denim vintage',
                'Modification col et encolure',
            ],
            'Customisation' => [
                'Broderie personnalisée sur veste / t-shirt',
                'Teinture écologique vêtement délavé',
                'Patchs thermocollants & brodés vintage',
                'Impression motif artisanal',
            ],
            'Upcycling' => [
                'Création veste patchwork à partir de chutes',
                'Confection accessoires mode zéro déchet',
            ],
        ];

        $type = fake()->randomElement(array_keys($services));
        $nom = fake()->randomElement($services[$type]);

        return [
            'atelier_id' => Atelier::factory(),
            'nom' => $nom,
            'type_service' => $type,
            'description' => fake()->paragraph(2),
            'tarif_estime' => fake()->randomFloat(2, 10, 120),
            'duree_estimee' => fake()->randomElement(['24h', '48h', '3 jours', '5 jours', '1 semaine']),
            'disponible' => fake()->boolean(90),
        ];
    }
}
