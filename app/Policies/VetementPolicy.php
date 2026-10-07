<?php

namespace App\Policies;

use App\Enums\StatutVetement;
use App\Enums\TypeVetement;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Vetement;

class VetementPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Admin, UserRole::Particulier], true);
    }

    public function update(User $user, Vetement $vetement): bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return $vetement->user_id === $user->id
            && $vetement->statut === StatutVetement::Disponible;
    }

    public function delete(User $user, Vetement $vetement): bool
    {
        return $this->update($user, $vetement);
    }

    public function demander(User $user, Vetement $vetement): bool
    {
        return $user->role === UserRole::Particulier
            && $vetement->user_id !== $user->id
            && $vetement->type === TypeVetement::Don
            && $vetement->statut === StatutVetement::Disponible;
    }
}