<?php

namespace App\Policies;

use App\Models\Jornada;
use App\Models\User;

class JornadaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Jornada $jornada): bool
    {
        if ($user->admin) {
            return true;
        }
        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');

        return $torneosGestionadosIds->contains($jornada->torneo_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Jornada $jornada): bool
    {
        if ($user->admin) {
            return true;
        }
        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');

        return $torneosGestionadosIds->contains($jornada->torneo_id);
    }

    public function delete(User $user, Jornada $jornada): bool
    {
        if ($user->admin) {
            return true;
        }
        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');

        return $torneosGestionadosIds->contains($jornada->torneo_id);
    }
}
