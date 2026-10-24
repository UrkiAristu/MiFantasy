<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLocalDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->admin && !$user->torneosGestionados()->exists()) {
            abort(403);
        }
        $torneos = $user ? $user->torneosGestionados : collect();
        $torneoId = $request->get("torneo_id");

        $torneosIds = $torneoId ? collect([$torneoId]) : $torneos->pluck("id");

        $torneosActivos = $torneosIds->isNotEmpty() ? $torneosIds->count() : 0;

        $equiposInscritos = $torneosIds->isNotEmpty() ? Equipo::whereHas("torneos", function ($q) use ($torneosIds) {
            $q->whereIn("torneos.id", $torneosIds);
        })->count() : 0;

        $jornadasCompletadas = $torneosIds->isNotEmpty() ? Jornada::whereIn("torneo_id", $torneosIds)
            ->whereHas("partidos", function ($q) {
                $q->where("estado", "!=", "jugado");
            }, "=", 0)
            ->whereHas("partidos")
            ->count() : 0;

        $partidosPendientes = $torneosIds->isNotEmpty() ? Partido::whereHas("jornada", function ($q) use ($torneosIds) {
            $q->whereIn("torneo_id", $torneosIds);
        })->where("estado", "programado")->count() : 0;

        $proximosPartidos = $torneosIds->isNotEmpty() ? Partido::with(["equipoLocal", "equipoVisitante", "jornada.torneo"])
            ->whereHas("jornada", function ($q) use ($torneosIds) {
                $q->whereIn("torneo_id", $torneosIds);
            })
            ->where("estado", "programado")
            ->orderBy("fecha_partido", "asc")
            ->take(3)
            ->get() : collect();

        return view("admin-local.dashboard", compact(
            "torneos",
            "torneoId",
            "torneosActivos",
            "equiposInscritos",
            "jornadasCompletadas",
            "partidosPendientes",
            "proximosPartidos"
        ));
    }

    public function equipos()
    {
        return redirect()->route("admin.local.equipos.index");
    }

    public function jornadasCreate()
    {
        return view("admin-local.jornadas-create");
    }
}
