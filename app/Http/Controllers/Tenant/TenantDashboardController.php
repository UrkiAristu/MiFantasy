<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Torneo;
use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Http\Request;

class TenantDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'torneos' => Torneo::count() ?? 0,
            'equipos' => Equipo::count() ?? 0,
            'jugadores' => Jugador::count() ?? 0,
            'partidos' => Partido::count() ?? 0,
        ];

        return view('tenant.dashboard', compact('data'));
    }
}
