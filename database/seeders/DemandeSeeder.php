<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Demande;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemandeSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('role', UserRole::Particulier)->get();
        $ateliers = User::where('role', UserRole::Atelier)->get();

        // Demandes prises en charge, avec 1 à 3 interventions chacune (le statut de la demande se synchronise tout seul).
        Demande::factory(10)->create([
            'user_id' => fn () => $clients->random()->id,
            'atelier_id' => fn () => $ateliers->random()->id,
        ])->each(function (Demande $demande) {
            Intervention::factory(rand(1, 3))->for($demande)->create(['atelier_id' => $demande->atelier_id]);
        });

        // Demandes encore en attente (sans intervention).
        Demande::factory(5)->create(['user_id' => fn () => $clients->random()->id]);
    }
}
