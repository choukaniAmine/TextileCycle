<?php

namespace App\Enums;

enum StatutDemande: string
{
    case EnAttente = 'en_attente';
    case Acceptee = 'acceptee';
    case Refusee = 'refusee';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Acceptee => 'Acceptée',
            self::Refusee => 'Refusée',
            self::Annulee => 'Annulée',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::EnAttente => 'warning',
            self::Acceptee => 'success',
            self::Refusee => 'danger',
            self::Annulee => 'secondary',
        };
    }
}