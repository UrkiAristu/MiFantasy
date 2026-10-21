<?php

namespace App\Policies;

use App\Models\Partido;
use App\Models\User;

class PartidoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Partido $partido): bool
    {
        if ($user->admin) {
            return true;
        }
        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');
        $torneoId = $partido->jornada?->torneo_id;
        return $torneoId && $torneosGestionadosIds->contains($torneoId);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Partido $partido): bool
    {
        if ($user->admin) {
            return true;
        }
        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');
        $torneoId = $partido->jornada?->torneo_id;
        return $torneoId && $torneosGestionadosIds->contains($torneoId);
    }

    public function delete(User $user, Partido $partido): bool
    {
        if ($user->admin) {
            return true;
        }
        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');
        $torneoId = $partido->jornada?->torneo_id;
        return $torneoId && $torneosGestionadosIds->contains($torneoId);
    }
}
