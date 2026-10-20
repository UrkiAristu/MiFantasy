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
    public function index(Request $request)
    {
        $user = Auth::user();
        $torneos = $user ? $user->torneosGestionados : collect();
        $torneoId = $request->get('torneo_id');

        $torneosIds = $torneoId ? collect([$torneoId]) : $torneos->pluck('id');

        $torneosActivos = $torneosIds->isNotEmpty() ? $torneosIds->count() : 0;

        $equiposInscritos = $torneosIds->isNotEmpty() ? Equipo::whereHas('torneos', function ($q) use ($torneosIds) {
            $q->whereIn('torneos.id', $torneosIds);
        })->count() : 0;

        $jugadoresBase = $torneosIds->isNotEmpty() ? Jugador::whereHas('participaciones', function ($q) use ($torneosIds) {
            $q->whereIn('torneos.id', $torneosIds);
        })->count() : 0;

        $partidosPendientes = $torneosIds->isNotEmpty() ? Partido::whereHas('jornada', function ($q) use ($torneosIds) {
            $q->whereIn('torneo_id', $torneosIds);
        })->where('estado', 'programado')->count() : 0;

        $ultimosPartidos = $torneosIds->isNotEmpty() ? Partido::with(['equipoLocal', 'equipoVisitante', 'jornada.torneo'])
            ->whereHas('jornada', function ($q) use ($torneosIds) {
                $q->whereIn('torneo_id', $torneosIds);
            })
            ->latest()
            ->take(5)
            ->get() : collect();

        return view('admin-local.dashboard', compact(
            'torneos',
            'torneoId',
            'torneosActivos',
            'equiposInscritos',
            'jugadoresBase',
            'partidosPendientes',
            'ultimosPartidos'
        ));
    }

    public function equipos()
    {
        return view('admin-local.equipos');
    }

    public function jornadasCreate()
    {
        return view('admin-local.jornadas-create');
    }
}
