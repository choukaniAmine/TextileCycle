<?php

namespace Database\Factories;

use App\Models\Atelier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Atelier>
 */
class AtelierFactory extends Factory
{
    public function definition(): array
    {
        $ateliers = [
            'Atelier Couture Éco-Responsable',
            'TexStyle Réparation & Customisation',
            'Maison Retouche Express',
            'Upcycle Lab Tunis',
            'La Petite Bobine',
            'Couture & Renaissance Textile',
            'Atelier Fil d\'Or',
            'Green Stitch Studio',
        ];

        $villesCoordinates = [
            'Tunis'    => [36.8065, 10.1815],
            'Ariana'   => [36.8663, 10.1956],
            'La Marsa' => [36.8782, 10.3247],
            'Sousse'   => [35.8256, 10.6369],
            'Sfax'     => [34.7406, 10.7603],
            'Nabeul'   => [36.4561, 10.7376],
            'Bizerte'  => [37.2746, 9.8739],
            'Monastir' => [35.7779, 10.8261],
        ];

        $ville = fake()->randomElement(array_keys($villesCoordinates));
        $baseCoords = $villesCoordinates[$ville];
        $lat = $baseCoords[0] + (fake()->numberBetween(-15, 15) / 1000);
        $lng = $baseCoords[1] + (fake()->numberBetween(-15, 15) / 1000);

        return [
            'nom' => fake()->unique()->randomElement($ateliers) . ' ' . fake()->unique()->numberBetween(1, 999),
            'description' => fake()->paragraph(3),
            'adresse' => fake()->streetAddress(),
            'ville' => $ville,
            'code_postal' => fake()->numerify('####'),
            'telephone' => '+216 ' . fake()->randomElement(['20', '21', '22', '24', '25', '29', '50', '52', '55', '98']) . ' ' . fake()->numerify('### ###'),
            'email' => fake()->safeEmail(),
            'image' => null,
            'horaires' => 'Lun - Sam: 09h00 - 18h30',
            'latitude' => round($lat, 7),
            'longitude' => round($lng, 7),
            'est_actif' => fake()->boolean(90),
        ];
    }
}
