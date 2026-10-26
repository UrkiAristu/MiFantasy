<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLocalDashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (! $user->admin && ! $user->torneosGestionados()->exists()) {
            abort(403);
        }

        $torneos = $user->torneosGestionados()->get();
        $torneosIds = $torneos->modelKeys();
        $torneoId = $request->query('torneo_id');

        if ($torneoId !== null && ! in_array((int) $torneoId, $torneosIds, true)) {
            abort(403);
        }

        $torneosActivos = count($torneosIds);
        $scopedTorneoIds = $torneoId !== null ? [(int) $torneoId] : $torneosIds;

        $equiposInscritos = $scopedTorneoIds !== []
            ? Equipo::whereHas('torneos', function ($query) use ($scopedTorneoIds): void {
                $query->whereIn('torneos.id', $scopedTorneoIds);
            })->count()
            : 0;

        $jugadoresBase = $scopedTorneoIds !== []
            ? Jugador::whereHas('participaciones', function ($query) use ($scopedTorneoIds): void {
                $query->whereIn('torneos.id', $scopedTorneoIds);
            })->count()
            : 0;

        $partidosQuery = Partido::whereHas('jornada', function ($query) use ($scopedTorneoIds): void {
            $query->whereIn('torneo_id', $scopedTorneoIds);
        });

        $partidosPendientes = $scopedTorneoIds !== []
            ? (clone $partidosQuery)->where('estado', 'programado')->count()
            : 0;

        $ultimosPartidos = $scopedTorneoIds !== []
            ? (clone $partidosQuery)
                ->with(['equipoLocal', 'equipoVisitante', 'jornada.torneo'])
                ->latest('fecha_partido')
                ->take(5)
                ->get()
            : collect();

        $jornadasCompletadas = $scopedTorneoIds !== []
            ? Jornada::whereIn('torneo_id', $scopedTorneoIds)
                ->whereHas('partidos')
                ->whereDoesntHave('partidos', function ($query): void {
                    $query->where('estado', '!=', 'jugado');
                })
                ->count()
            : 0;

        return view('admin-local.dashboard', compact(
            'torneos',
            'torneoId',
            'torneosActivos',
            'equiposInscritos',
            'jugadoresBase',
            'partidosPendientes',
            'ultimosPartidos',
            'jornadasCompletadas'
        ))->with('proximosPartidos', $ultimosPartidos->where('estado', 'programado')->take(3));
    }

    public function equipos()
    {
        return redirect()->route('admin.local.equipos.index');
    }

    public function jornadasCreate()
    {
        return view('admin-local.jornadas-create');
    }
}
