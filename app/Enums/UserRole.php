<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Particulier = 'particulier';
    case Atelier = 'atelier';
    case Association = 'association';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Particulier => 'Particulier',
            self::Atelier => 'Atelier',
            self::Association => 'Association',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Particulier => 'info',
            self::Atelier => 'warning',
            self::Association => 'success',
        };
    }

    /** Rôles autorisés à l'inscription publique (l'admin est créé en back office). */
    public static function publicValues(): array
    {
        return [self::Particulier->value, self::Atelier->value, self::Association->value];
    }
}
