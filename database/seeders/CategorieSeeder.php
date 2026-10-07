<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    foreach (['Pantalon', 'Chemise', 'Veste', 'Robe', 'Chaussures', 'Accessoires'] as $nom) {
        \App\Models\Categorie::firstOrCreate(['nom' => $nom]);
    }
}
}
