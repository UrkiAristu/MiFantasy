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
        $torneosIds = $user->torneosGestionados()->pluck('torneos.id');

        $torneosActivos = $torneosIds->count();

        $equiposInscritos = Equipo::whereHas('torneos', function ($q) use ($torneosIds) {
            $q->whereIn('torneos.id', $torneosIds);
        })->count();

        $jugadoresBase = Jugador::whereHas('participaciones', function ($q) use ($torneosIds) {
            $q->whereIn('torneos.id', $torneosIds);
        })->count();

        $partidosPendientes = Partido::whereHas('jornada', function ($q) use ($torneosIds) {
            $q->whereIn('torneo_id', $torneosIds);
        })->where('estado', 'programado')->count();

        return view('admin-local.dashboard', compact(
            'torneosActivos',
            'equiposInscritos',
            'jugadoresBase',
            'partidosPendientes'
        ));
    }
}
