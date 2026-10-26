<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Torneo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLocalEquipoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;
        $torneoId = $request->get('torneo_id');

        $query = Equipo::with(['torneos', 'jugadores']);

        if (! $user->admin) {
            $torneosGestionadosIds = $torneos->pluck('id');
            $query->whereHas('torneos', function ($q) use ($torneosGestionadosIds) {
                $q->whereIn('torneos.id', $torneosGestionadosIds);
            });
        }

        if ($torneoId) {
            $query->whereHas('torneos', function ($q) use ($torneoId) {
                $q->where('torneos.id', $torneoId);
            });
        }

        if ($search = $request->get('search')) {
            $query->where('nombre', 'like', "%{$search}%");
        }

        $equipos = $query->latest()->paginate(10)->withQueryString();

        return view('admin-local.equipos.index', compact('equipos', 'torneos', 'torneoId'));
    }
}
