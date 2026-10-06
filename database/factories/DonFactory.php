<?php

namespace Database\Factories;

use App\Enums\DonStatus;
use App\Models\Association;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonFactory extends Factory
{
    public function definition(): array
    {
        $pieces = [
            'pantalons', 'vestes', 'chemises', 'pulls', 'robes', 'manteaux', 'chaussures', 'écharpes',
        ];
        $a = fake()->numberBetween(1, 5);
        $b = fake()->numberBetween(1, 4);

        return [
            // Relations : un don appartient à une association et à un donateur.
            'association_id' => Association::factory(),
            'user_id' => User::factory(),
            'description' => $a.' '.fake()->randomElement($pieces).', '.$b.' '.fake()->randomElement($pieces),
            'quantity' => $a + $b,
            'status' => fake()->randomElement(DonStatus::cases()),
            'donated_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    public function status(DonStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
