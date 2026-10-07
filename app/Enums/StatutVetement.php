<?php

namespace App\Enums;

enum StatutVetement: string
{
    case Disponible = 'disponible';
    case Donne = 'donne';

    public function label(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::Donne => 'Donné',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Disponible => 'success',
            self::Donne => 'secondary',
        };
    }
}