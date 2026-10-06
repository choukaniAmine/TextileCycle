<?php

namespace App\Enums;

/** Statut commun aux demandes et aux interventions : En attente → En cours → Terminée (ou Annulée). */
enum Statut: string
{
    case EnAttente = 'en_attente';
    case EnCours = 'en_cours';
    case Terminee = 'terminee';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::EnCours => 'En cours',
            self::Terminee => 'Terminée',
            self::Annulee => 'Annulée',
        };
    }

    /** Couleur Bootstrap (bg-* en front, badge-* en back). */
    public function color(): string
    {
        return match ($this) {
            self::EnAttente => 'warning',
            self::EnCours => 'info',
            self::Terminee => 'success',
            self::Annulee => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::EnAttente => 'bi-hourglass-split',
            self::EnCours => 'bi-scissors',
            self::Terminee => 'bi-check2-circle',
            self::Annulee => 'bi-x-circle',
        };
    }

    public function step(): int
    {
        return match ($this) {
            self::EnAttente => 1,
            self::EnCours => 2,
            self::Terminee => 3,
            self::Annulee => 0,
        };
    }

    public function next(): ?self
    {
        return match ($this) {
            self::EnAttente => self::EnCours,
            self::EnCours => self::Terminee,
            default => null,
        };
    }
}
