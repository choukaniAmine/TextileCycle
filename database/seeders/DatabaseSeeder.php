<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Module commun : utilisateurs
        User::factory()->role(UserRole::Admin)->create([
            'name' => 'Administrateur', 'email' => 'admin@refashion.test', 'city' => 'Tunis',
        ]);
        User::factory()->role(UserRole::Particulier)->create([
            'name' => 'Amel Particulier', 'email' => 'particulier@refashion.test', 'city' => 'Tunis',
        ]);
        User::factory()->role(UserRole::Atelier)->create([
            'name' => 'Sami Couturier', 'email' => 'atelier@refashion.test', 'organization' => 'Atelier Fil & Aiguille', 'city' => 'Sfax',
        ]);
        User::factory()->role(UserRole::Association)->create([
            'name' => 'Nour Bénévole', 'email' => 'association@refashion.test', 'organization' => 'Association Seconde Vie', 'city' => 'Sousse',
        ]);

        User::factory(8)->create();
        User::factory(3)->role(UserRole::Atelier)->create();
        User::factory(3)->role(UserRole::Association)->create();

        // Module 2 : ateliers, services et avis
        $this->call(AtelierSeeder::class);

        // Module 3 : associations et dons
        $this->call(AssociationSeeder::class);

        // Module 4 : demandes de réparation / transformation et interventions
        $this->call(DemandeSeeder::class);
        $this->call(CategorieSeeder::class);
    }
}