<?php

namespace App\Policies;

use App\Models\Torneo;
use App\Models\User;

class TorneoPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->id;
    }

    public function view(User $user, Torneo $torneo): bool
    {
        if ($user->admin) {
            return true;
        }

        return $user->torneosGestionados->contains($torneo->id);
    }

    public function create(User $user): bool
    {
        return $user->admin;
    }

    public function update(User $user, Torneo $torneo): bool
    {
        if ($user->admin) {
            return true;
        }

        return $user->torneosGestionados->contains($torneo->id);
    }

    public function delete(User $user, Torneo $torneo): bool
    {
        if ($user->admin) {
            return true;
        }

        return $user->torneosGestionados->contains($torneo->id);
    }
}
