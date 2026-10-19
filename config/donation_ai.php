<?php

return [
    // On cible exactement les noms de vos cases de l'Enum (Accepte, Livre, Refuse...)
    'accepted_keywords' => ['Accepte', 'Livre'],
    'refused_keywords'  => ['Refuse'],

    // Les poids restent les mêmes (40% IA, 40% ancienneté, 20% quantité)
    'priority_weights' => [
        'acceptance' => 0.40,
        'staleness'  => 0.40,
        'quantity'   => 0.20,
    ],

    // Mots-clés spécifiques aux vêtements et au textile
    'keyword_boost' => [

    // 1. URGENCE ABSOLUE (+40 points)
    'urgence' => [
        'weight' => 0.40,
        'keywords' => [
            // Urgence et détresse
            'urgent', 'urgence', 'urgemment', 'prioritaire',
            'détresse', 'detresse', 'situation critique',
            'besoin immédiat', 'aide immédiate',
            'grande précarité', 'sans ressources',

            // Personnes sans abri
            'sans abri', 'sans-abri', 'sans domicile',
            'sans domicile fixe', 'sdf', 'à la rue',
            'dormir dehors', 'vit dehors', 'hébergement d’urgence',
            'famille sans logement',

            // Conditions climatiques
            'hiver', 'grand froid', 'froid intense',
            'températures négatives', 'gel', 'gelée',
            'intempéries', 'pluie', 'neige',
            'se protéger du froid', 'protection thermique',

            // Personnes en situation de vulnérabilité
            'bébé', 'bebe', 'nouveau-né', 'nouveau né',
            'nourrisson', 'enfant abandonné',
            'enfant en difficulté', 'famille démunie',
            'personne démunie', 'personne vulnérable',
            'personne en difficulté', 'famille en détresse',
            'vêtements indispensables', 'manque de vêtements',
            'aucun vêtement', 'sans vêtements',
            'vêtements chauds indispensables'
        ],
    ],

    // 2. VÊTEMENTS POUR PERSONNES VULNÉRABLES (+20 points)
    'vulnerable' => [
        'weight' => 0.20,
        'keywords' => [
            // Enfants et bébés
            'enfant', 'enfants', 'bébé', 'bebe',
            'nouveau-né', 'nourrisson', 'naissance',
            'layette', 'vêtements bébé', 'habits bébé',
            'vêtements enfant', 'habits enfant',
            'pyjama bébé', 'body bébé', 'grenouillère',
            'chaussettes bébé', 'bonnet bébé',

            // Vêtements chauds
            'manteau', 'manteaux', 'couverture',
            'couvertures', 'pull', 'pull-over',
            'pull en laine', 'doudoune', 'veste',
            'veste chaude', 'imperméable', 'anorak',
            'parka', 'blouson', 'gilet', 'cardigan',
            'sweat', 'sweat-shirt', 'polairе',
            'écharpe', 'bonnet', 'gants', 'moufles',
            'chaussettes chaudes', 'collants thermiques',

            // Autres personnes vulnérables
            'personne âgée', 'personnes âgées',
            'senior', 'seniors', 'handicap',
            'personne handicapée', 'famille nécessiteuse',
            'famille à faible revenu', 'famille pauvre',
            'personne en précarité', 'personne isolée',

            // Literie et protection
            'couette', 'édredon', 'duvet',
            'sac de couchage', 'drap chaud',
            'linge de lit', 'couverture thermique'
        ],
    ],

    // 3. VÊTEMENTS ET LINGE DE BASE (+10 points)
    'standard' => [
        'weight' => 0.10,
        'keywords' => [
            // Catégorie générale
            'vêtement', 'vêtements', 'vetement', 'vetements',
            'habit', 'habits', 'habillement',
            'textile', 'textiles', 'fringues',
            'don de vêtements', 'donner des vêtements',
            'collecte de vêtements', 'vestiaire solidaire',

            // Hauts
            't-shirt', 'tee-shirt', 'tee shirt',
            'chemise', 'chemisier', 'polo',
            'blouse', 'tunique', 'débardeur',
            'top', 'sweat-shirt', 'pull',

            // Bas
            'pantalon', 'jean', 'jeans', 'short',
            'bermuda', 'jupe', 'legging', 'jogging',
            'survêtement', 'robe', 'combinaison',

            // Chaussures
            'chaussure', 'chaussures', 'basket',
            'baskets', 'bottines', 'bottes',
            'sandales', 'pantoufles', 'chaussons',
            'espadrilles', 'chaussures de sport',

            // Sous-vêtements
            'sous-vêtements', 'sous vetements',
            'culotte', 'caleçon', 'slip', 'soutien-gorge',
            'chaussettes', 'collants', 'maillot de corps',

            // Linge et accessoires
            'linge', 'linge de maison', 'draps',
            'serviette', 'serviettes', 'taie d’oreiller',
            'taie oreiller', 'torchon', 'peignoir',
            'sac en tissu', 'bonnet', 'écharpe',
            'ceinture', 'sac à main'
        ],
    ],
],

    'min_training_samples' => 10,
];