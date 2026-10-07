<?php

namespace App\Enums;

enum TypeVetement: string
{
    case Don = 'don';
    case Reparation = 'reparation';
    case Transformation = 'transformation';

    public function label(): string
    {
        return match ($this) {
            self::Don => 'Don',
            self::Reparation => 'Réparation',
            self::Transformation => 'Transformation',
        };
    }
}