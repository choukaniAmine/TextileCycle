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

        $villes = ['Tunis', 'Ariana', 'La Marsa', 'Sousse', 'Sfax', 'Nabeul', 'Bizerte', 'Monastir'];

        return [
            'nom' => fake()->unique()->randomElement($ateliers) . ' ' . fake()->unique()->numberBetween(1, 999),
            'description' => fake()->paragraph(3),
            'adresse' => fake()->streetAddress(),
            'ville' => fake()->randomElement($villes),
            'code_postal' => fake()->numerify('####'),
            'telephone' => '+216 ' . fake()->randomElement(['20', '21', '22', '24', '25', '29', '50', '52', '55', '98']) . ' ' . fake()->numerify('### ###'),
            'email' => fake()->safeEmail(),
            'image' => null,
            'horaires' => 'Lun - Sam: 09h00 - 18h30',
            'est_actif' => fake()->boolean(90),
        ];
    }
}
