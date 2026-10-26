<?php

namespace App\Policies;

use App\Models\Equipo;
use App\Models\User;

class EquipoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Equipo $equipo): bool
    {
        if ($user->admin) {
            return true;
        }

        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');

        return $equipo->torneos()->whereIn('torneos.id', $torneosGestionadosIds)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Equipo $equipo): bool
    {
        if ($user->admin) {
            return true;
        }

        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');

        return $equipo->torneos()->whereIn('torneos.id', $torneosGestionadosIds)->exists();
    }

    public function delete(User $user, Equipo $equipo): bool
    {
        if ($user->admin) {
            return true;
        }

        $torneosGestionadosIds = $user->torneosGestionados()->pluck('torneos.id');

        return $equipo->torneos()->whereIn('torneos.id', $torneosGestionadosIds)->exists();
    }
}
