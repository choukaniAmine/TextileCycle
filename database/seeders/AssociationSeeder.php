<?php

namespace Database\Seeders;

use App\Enums\DonStatus;
use App\Models\Association;
use App\Models\Don;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssociationSeeder extends Seeder
{
    public function run(): void
    {
        $donors = User::query()->where('role', 'particulier')->get();

        // Comptes « association » : chacun gère une association (demo : association@refashion.test → Espoir Tunisie).
        $managers = User::query()->where('role', 'association')->orderBy('id')->get();

        // Exemple du cahier des charges : Espoir Tunisie reçoit 3 pantalons + 2 vestes (en attente).
        $espoir = Association::factory()->create([
            'manager_id' => $managers->shift()?->id,
            'name' => 'Espoir Tunisie',
            'description' => 'Distribue des vêtements aux familles en difficulté.',
            'city' => 'Tunis',
        ]);
        Don::factory()->for($espoir)->for($donors->first() ?? User::factory()->create(), 'donor')->create([
            'description' => '3 pantalons, 2 vestes',
            'quantity' => 5,
            'status' => DonStatus::EnAttente,
            'donated_at' => now()->toDateString(),
        ]);

        // Autres associations, chacune avec plusieurs dons (relation 1-N).
        Association::factory(5)->create()->each(function (Association $association) use ($donors, $managers) {
            // Les comptes restants sont rattachés aux associations suivantes (un compte = une association).
            $association->update(['manager_id' => $managers->shift()?->id]);

            Don::factory(fake()->numberBetween(2, 6))
                ->for($association)
                ->state(fn () => ['user_id' => $donors->isNotEmpty() ? $donors->random()->id : User::factory()])
                ->create();
        });

        Association::factory()->inactive()->create(['name' => 'Association Dormante']);
    }
}
