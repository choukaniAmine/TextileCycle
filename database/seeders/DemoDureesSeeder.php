<?php

namespace Database\Seeders;

use App\Enums\Statut;
use App\Enums\UserRole;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ⚠ DONNÉES DE DÉMONSTRATION UNIQUEMENT ⚠
 *
 * Ces durées sont SIMULÉES par une formule + du bruit aléatoire. Elles ne reflètent aucun travail réel.
 * Elles servent à montrer le fonctionnement du pipeline (export → entraînement → estimation) quand
 * aucune demande réelle n'a encore été terminée. Chaque ligne est marquée duree_reelle_source = 'demo'
 * et l'interface affiche un avertissement tant que le modèle les utilise.
 *
 * Pour un entraînement sur données réelles uniquement : php artisan ml:train --reel-seulement
 *
 * Lancement : php artisan db:seed --class=DemoDureesSeeder
 */
class DemoDureesSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('role', UserRole::Particulier)->pluck('id');
        $ateliers = User::where('role', UserRole::Atelier)->pluck('id');
        if ($clients->isEmpty() || $ateliers->isEmpty()) {
            $this->command?->error('Lancez d’abord DatabaseSeeder (utilisateurs requis).');

            return;
        }

        $base = ['dechirure' => 60, 'fermeture' => 75, 'bouton' => 20, 'ourlet' => 35, 'doublure' => 90,
            'couture' => 30, 'retouche_taille' => 110, 'transformation_coupe' => 210, 'upcycling' => 180];
        $facteurCat = ['jean' => 1.15, 'pantalon' => 1.0, 'chemise' => 0.95, 'tshirt_pull' => 0.8, 'robe' => 1.1,
            'jupe' => 0.9, 'veste' => 1.2, 'manteau' => 1.35, 'costume' => 1.4, 'autre' => 1.0];
        $facteurNiveau = [1 => 0.75, 2 => 1.0, 3 => 1.5];

        for ($i = 0; $i < 90; $i++) {
            $int = array_rand($base);
            $cat = array_rand($facteurCat);
            $niveau = random_int(1, 3);
            $bruit = exp((mt_rand() / mt_getrandmax() - 0.5) * 0.5); // ≈ ±25 %
            $minutes = max(5, (int) (round($base[$int] * $facteurCat[$cat] * $facteurNiveau[$niveau] * $bruit / 5) * 5));

            Demande::factory()->create([
                'user_id' => $clients->random(),
                'atelier_id' => $ateliers->random(),
                'type' => in_array($int, ['transformation_coupe', 'upcycling'], true) ? 'transformation' : 'reparation',
                'categorie_vetement' => $cat,
                'type_intervention' => $int,
                'niveau_difficulte' => $niveau,
                'statut' => Statut::Terminee,
                'duree_reelle_min' => $minutes,
                'duree_reelle_source' => 'demo',
                'duree_reelle_confirmee_at' => now(),
            ]);
        }

        $this->command?->warn('90 demandes DE DÉMONSTRATION créées (durées simulées). Ne pas présenter comme des données réelles.');
    }
}
