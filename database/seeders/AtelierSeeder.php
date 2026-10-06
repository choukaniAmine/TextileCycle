<?php

namespace Database\Seeders;

use App\Models\Atelier;
use App\Models\Avis;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class AtelierSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $ateliersData = [
            [
                'nom' => 'EcoStyle Atelier & Retouches',
                'description' => 'Atelier spécialisé dans la retouche moderne, le sauvetage de vêtements anciens et la réparation zéro déchet.',
                'adresse' => '12 Avenue Habib Bourguiba',
                'ville' => 'Tunis',
                'code_postal' => '1001',
                'telephone' => '+216 71 234 567',
                'email' => 'contact@ecostyle-atelier.tn',
                'horaires' => 'Lun - Ven: 08h30 - 18h30 | Sam: 09h00 - 15h00',
                'est_actif' => true,
                'services' => [
                    [
                        'nom' => 'Réparation fermeture éclair et accrocs',
                        'type_service' => 'Reparation',
                        'description' => 'Changement complet de zip ou réparation sur vestes, jeans et manteaux.',
                        'tarif_estime' => 25.00,
                        'duree_estimee' => '24h',
                        'disponible' => true,
                    ],
                    [
                        'nom' => 'Ourlet & Ajustement taille pantalon',
                        'type_service' => 'Retouche',
                        'description' => 'Ourlet simple ou invisible, reprise taille et ajustement de coupe.',
                        'tarif_estime' => 15.00,
                        'duree_estimee' => '24h',
                        'disponible' => true,
                    ],
                    [
                        'nom' => 'Transformation robe / jupe',
                        'type_service' => 'Transformation',
                        'description' => 'Modernisation d\'anciennes tenues, transformation de robes en jupes ou ensembles.',
                        'tarif_estime' => 50.00,
                        'duree_estimee' => '3 jours',
                        'disponible' => true,
                    ],
                ],
                'avis' => [
                    ['note' => 5, 'commentaire' => 'Travail exceptionnel ! Ma veste préférée a retrouvé une seconde jeunesse.'],
                    ['note' => 5, 'commentaire' => 'Rapide, très soigné et accueil très chaleureux. Je recommande à 100%.'],
                    ['note' => 4, 'commentaire' => 'Bon travail sur mes ourlets de pantalons, fini dans les délais.'],
                ]
            ],
            [
                'nom' => 'Upcycle Lab Carthage',
                'description' => 'Laboratoire de création textile durable et d\'upcycling créatif. Donnez une seconde vie élégante à vos pièces.',
                'adresse' => '45 Rue de la Plage',
                'ville' => 'La Marsa',
                'code_postal' => '2078',
                'telephone' => '+216 22 987 654',
                'email' => 'hello@upcyclelab.tn',
                'horaires' => 'Mar - Sam: 10h00 - 19h00',
                'est_actif' => true,
                'services' => [
                    [
                        'nom' => 'Customisation & Broderie artistique',
                        'type_service' => 'Customisation',
                        'description' => 'Motifs brodés personnalisés sur vestes denim ou t-shirts vintage.',
                        'tarif_estime' => 35.00,
                        'duree_estimee' => '48h',
                        'disponible' => true,
                    ],
                    [
                        'nom' => 'Upcycling Denim : Jean vers Sac Tote-Bag',
                        'type_service' => 'Upcycling',
                        'description' => 'Confection d\'un sac robuste et stylé à partir de vos vieux jeans abîmés.',
                        'tarif_estime' => 40.00,
                        'duree_estimee' => '2 jours',
                        'disponible' => true,
                    ],
                ],
                'avis' => [
                    ['note' => 5, 'commentaire' => 'Le sac créé à partir de mon vieux jean est magnifique et très solide !'],
                    ['note' => 4, 'commentaire' => 'Créativité au top, démarche écologique exemplaire.'],
                ]
            ],
            [
                'nom' => 'Maison Couture Sousse',
                'description' => 'Maîtres tailleurs et couturiers avec plus de 20 ans d\'expertise dans la retouche haute précision.',
                'adresse' => '88 Boulevard du 14 Janvier',
                'ville' => 'Sousse',
                'code_postal' => '4000',
                'telephone' => '+216 73 555 444',
                'email' => 'maison.couture.sousse@gmail.com',
                'horaires' => 'Lun - Sam: 09h00 - 19h00',
                'est_actif' => true,
                'services' => [
                    [
                        'nom' => 'Reprise complète costume & veste',
                        'type_service' => 'Retouche',
                        'description' => 'Ajustement épaules, cintrage et retouche manches pour un tombé impeccable.',
                        'tarif_estime' => 70.00,
                        'duree_estimee' => '4 jours',
                        'disponible' => true,
                    ],
                    [
                        'nom' => 'Teinture végétale pour textiles décolorés',
                        'type_service' => 'Transformation',
                        'description' => 'Ravivez vos vêtements passés avec des teintures écologiques respectueuses de la fibre.',
                        'tarif_estime' => 30.00,
                        'duree_estimee' => '48h',
                        'disponible' => true,
                    ],
                ],
                'avis' => [
                    ['note' => 5, 'commentaire' => 'Un travail de maître pour mon costume, ajustement parfait.'],
                ]
            ],
            [
                'nom' => 'Sfax Retouche & Seconde Vie',
                'description' => 'Service rapide et économique de réparation textile pour tous vos vêtements du quotidien.',
                'adresse' => '15 Rue Hédi Chaker',
                'ville' => 'Sfax',
                'code_postal' => '3000',
                'telephone' => '+216 74 111 222',
                'email' => 'contact@sfax-retouche.tn',
                'horaires' => 'Lun - Ven: 08h00 - 17h30',
                'est_actif' => true,
                'services' => [
                    [
                        'nom' => 'Remplacement de doublure manteau',
                        'type_service' => 'Reparation',
                        'description' => 'Remplacement intégral de la doublure intérieure usée.',
                        'tarif_estime' => 45.00,
                        'duree_estimee' => '3 jours',
                        'disponible' => true,
                    ],
                ],
                'avis' => [
                    ['note' => 4, 'commentaire' => 'Très bon rapport qualité-prix et travail propre.'],
                ]
            ]
        ];

        foreach ($ateliersData as $data) {
            $services = $data['services'];
            $avisList = $data['avis'] ?? [];
            unset($data['services'], $data['avis']);

            $atelier = Atelier::create($data);

            foreach ($services as $serviceData) {
                $serviceData['atelier_id'] = $atelier->id;
                Service::create($serviceData);
            }

            // Créer les avis pour cet atelier
            foreach ($avisList as $index => $avisData) {
                $user = $users->get($index % max($users->count(), 1));
                if ($user) {
                    Avis::create([
                        'atelier_id'  => $atelier->id,
                        'user_id'     => $user->id,
                        'note'        => $avisData['note'],
                        'commentaire' => $avisData['commentaire'],
                    ]);
                }
            }
        }

        // 4 ateliers supplémentaires générés avec la factory
        $factoryAteliers = Atelier::factory()
            ->count(4)
            ->has(Service::factory()->count(3))
            ->create();

        // Ajouter des avis aléatoires pour ces ateliers factory
        foreach ($factoryAteliers as $atelier) {
            if ($users->isNotEmpty()) {
                $randomUsers = $users->random(min(2, $users->count()));
                foreach ($randomUsers as $user) {
                    Avis::create([
                        'atelier_id'  => $atelier->id,
                        'user_id'     => $user->id,
                        'note'        => rand(4, 5),
                        'commentaire' => 'Service de retouche très satisfaisant et respectueux des délais.',
                    ]);
                }
            }
        }
    }
}
