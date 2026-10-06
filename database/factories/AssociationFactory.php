<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AssociationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company().' Solidarité',
            'description' => fake()->sentence(12),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->randomElement(['Tunis', 'Sfax', 'Sousse', 'Bizerte', 'Nabeul', 'Gabès']),
            'address' => fake()->streetAddress(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
