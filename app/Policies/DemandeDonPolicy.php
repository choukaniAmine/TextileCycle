<?php

namespace App\Policies;

use App\Enums\StatutDemande;
use App\Enums\StatutVetement;
use App\Models\DemandeDon;
use App\Models\User;

class DemandeDonPolicy
{
    public function repondre(User $user, DemandeDon $demande): bool
    {
        return $demande->vetement->user_id === $user->id
            && $demande->statut === StatutDemande::EnAttente
            && $demande->vetement->statut === StatutVetement::Disponible;
    }

    public function annuler(User $user, DemandeDon $demande): bool
    {
        return $demande->demandeur_id === $user->id
            && $demande->statut === StatutDemande::EnAttente;
    }
}