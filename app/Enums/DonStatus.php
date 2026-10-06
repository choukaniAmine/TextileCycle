<?php

namespace App\Enums;

enum DonStatus: string
{
    case EnAttente = 'en_attente';
    case Accepte = 'accepte';
    case EnCours = 'en_cours';
    case Livre = 'livre';
    case Refuse = 'refuse';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Accepte => 'Accepté',
            self::EnCours => 'En cours de collecte',
            self::Livre => 'Livré',
            self::Refuse => 'Refusé',
        };
    }

    /** Classe de couleur Bootstrap (badge-* en back office, bg-* en front office). */
    public function badge(): string
    {
        return match ($this) {
            self::EnAttente => 'warning',
            self::Accepte => 'info',
            self::EnCours => 'primary',
            self::Livre => 'success',
            self::Refuse => 'danger',
        };
    }

    /**
     * Étapes que l'association peut choisir depuis l'état courant.
     * En attente → Accepté ou Refusé ; Accepté → En cours ; En cours → Livré.
     *
     * @return array<int, DonStatus>
     */
    public function transitions(): array
    {
        return match ($this) {
            self::EnAttente => [self::Accepte, self::Refuse],
            self::Accepte => [self::EnCours],
            self::EnCours => [self::Livre],
            default => [],
        };
    }

    public function canBecome(self $next): bool
    {
        return in_array($next, $this->transitions(), true);
    }

    /** Libellé du bouton d'action correspondant à cette étape. */
    public function actionLabel(): string
    {
        return match ($this) {
            self::Accepte => 'Accepter',
            self::Refuse => 'Refuser',
            self::EnCours => 'Collecte en cours',
            self::Livre => 'Marquer livré',
            default => $this->label(),
        };
    }

    /** Un don n'est plus modifiable par le donateur dès qu'il a été traité. */
    public function isFinal(): bool
    {
        return in_array($this, [self::Livre, self::Refuse], true);
    }
}
