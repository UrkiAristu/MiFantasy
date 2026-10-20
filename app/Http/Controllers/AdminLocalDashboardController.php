<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLocalDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $torneosIds = $user ? $user->torneosGestionados()->pluck('torneos.id') : collect();

        $torneosActivos = $torneosIds->count();

        $equiposInscritos = $torneosIds->isNotEmpty() ? Equipo::whereHas('torneos', function ($q) use ($torneosIds) {
            $q->whereIn('torneos.id', $torneosIds);
        })->count() : 0;

        $jugadoresBase = $torneosIds->isNotEmpty() ? Jugador::whereHas('participaciones', function ($q) use ($torneosIds) {
            $q->whereIn('torneos.id', $torneosIds);
        })->count() : 0;

        $partidosPendientes = $torneosIds->isNotEmpty() ? Partido::whereHas('jornada', function ($q) use ($torneosIds) {
            $q->whereIn('torneo_id', $torneosIds);
        })->where('estado', 'programado')->count() : 0;

        $ultimosPartidos = $torneosIds->isNotEmpty() ? Partido::with(['equipoLocal', 'equipoVisitante', 'jornada'])
            ->whereHas('jornada', function ($q) use ($torneosIds) {
                $q->whereIn('torneo_id', $torneosIds);
            })
            ->latest()
            ->take(5)
            ->get() : collect();

        return view('admin-local.dashboard', compact(
            'torneosActivos',
            'equiposInscritos',
            'jugadoresBase',
            'partidosPendientes',
            'ultimosPartidos'
        ));
    }
}
