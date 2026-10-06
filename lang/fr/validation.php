<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'required_if' => 'Le champ :attribute est obligatoire pour ce type de compte.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'unique' => 'Cette valeur de :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'max' => ['string' => 'Le champ :attribute ne peut pas dépasser :max caractères.'],
    'min' => ['string' => 'Le champ :attribute doit contenir au moins :min caractères.'],
    'in' => 'La valeur choisie pour :attribute est invalide.',
    'enum' => 'La valeur choisie pour :attribute est invalide.',
    'password' => [
        'letters' => 'Le :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le :attribute doit contenir une majuscule et une minuscule.',
        'numbers' => 'Le :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le :attribute doit contenir au moins un symbole.',
    ],
    'attributes' => [
        'name' => 'nom', 'email' => 'e-mail', 'password' => 'mot de passe', 'role' => 'rôle',
        'phone' => 'téléphone', 'city' => 'ville', 'organization' => 'organisation', 'bio' => 'présentation',
    ],
];
