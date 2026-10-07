<?php

namespace App\Enums;

enum EtatVetement: string
{
    case Neuf = 'neuf';
    case TresBon = 'tres_bon';
    case Bon = 'bon';
    case Use = 'use';

    public function label(): string
    {
        return match ($this) {
            self::Neuf => 'Neuf',
            self::TresBon => 'Très bon',
            self::Bon => 'Bon',
            self::Use => 'Usé',
        };
    }
}