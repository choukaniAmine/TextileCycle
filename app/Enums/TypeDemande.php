<?php

namespace App\Enums;

enum TypeDemande: string
{
    case Reparation = 'reparation';
    case Transformation = 'transformation';

    public function label(): string
    {
        return match ($this) {
            self::Reparation => 'Réparation',
            self::Transformation => 'Transformation',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Reparation => '🧵',
            self::Transformation => '✨',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Reparation => 'Déchirure, fermeture cassée, bouton, ourlet…',
            self::Transformation => 'Changer la coupe, upcycling, nouvelle pièce…',
        };
    }
}
