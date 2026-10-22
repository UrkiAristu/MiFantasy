<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLocalPartidoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;
        $torneoId = $request->get('torneo_id');
        $jornadaId = $request->get('jornada_id');

        $query = Partido::with(['jornada.torneo', 'equipoLocal', 'equipoVisitante']);

        if (!$user->admin) {
            $torneosGestionadosIds = $torneos->pluck('id');
            $query->whereHas('jornada', function ($q) use ($torneosGestionadosIds) {
                $q->whereIn('torneo_id', $torneosGestionadosIds);
            });
        }

        if ($torneoId) {
            $query->whereHas('jornada', function ($q) use ($torneoId) {
                $q->where('torneo_id', $torneoId);
            });
        }

        if ($jornadaId) {
            $query->where('jornada_id', $jornadaId);
        }

        $partidos = $query->latest('fecha_partido')->paginate(10)->withQueryString();
        $jornadas = $torneoId ? Jornada::where('torneo_id', $torneoId)->get() : Jornada::whereIn('torneo_id', $torneos->pluck('id'))->get();

        return view('admin-local.partidos.index', compact('partidos', 'torneos', 'torneoId', 'jornadas', 'jornadaId'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;
        $torneosGestionadosIds = $torneos->pluck('id');

        $jornadas = Jornada::whereIn('torneo_id', $torneosGestionadosIds)->with('torneo')->get();

        // Obtener solo los equipos que participan en los torneos gestionados por este admin local
        $equipos = Equipo::whereHas('torneos', function ($q) use ($torneosGestionadosIds) {
            $q->whereIn('torneos.id', $torneosGestionadosIds);
        })->get();

        return view('admin-local.partidos.create', compact('jornadas', 'equipos'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $torneosGestionadosIds = $user->admin ? Torneo::pluck('id') : $user->torneosGestionados->pluck('id');

        $validated = $request->validate([
            'jornada_id' => [
                'required',
                'exists:jornadas,id',
                function ($attribute, $value, $fail) use ($torneosGestionadosIds) {
                    $jornada = Jornada::find($value);
                    if (!$jornada || !$torneosGestionadosIds->contains($jornada->torneo_id)) {
                        $fail('La jornada seleccionada no pertenece a un torneo bajo tu gestión.');
                    }
                },
            ],
            'equipo_local_id' => 'required|exists:equipos,id|different:equipo_visitante_id',
            'equipo_visitante_id' => 'required|exists:equipos,id',
            'fecha_partido' => 'required|date',
            'goles_local' => 'nullable|integer|min:0',
            'goles_visitante' => 'nullable|integer|min:0',
            'estado' => 'required|in:programado,jugado,cancelado',
        ]);

        $partido = Partido::create($validated);

        if ($partido->estado === 'jugado') {
            $partido->actualizarEstadisticas();
        }

        return redirect()->route('admin.local.partidos.index')->with('success', 'Partido registrado correctamente.');
    }

    public function edit(Partido $partido)
    {
        $this->authorize('update', $partido);

        $user = Auth::user();
        $torneosGestionadosIds = $user->admin ? Torneo::pluck('id') : $user->torneosGestionados->pluck('id');

        $jornadas = Jornada::whereIn('torneo_id', $torneosGestionadosIds)->with('torneo')->get();
        $equipos = Equipo::whereHas('torneos', function ($q) use ($torneosGestionadosIds) {
            $q->whereIn('torneos.id', $torneosGestionadosIds);
        })->get();

        return view('admin-local.partidos.edit', compact('partido', 'jornadas', 'equipos'));
    }

    public function update(Request $request, Partido $partido)
    {
        $this->authorize('update', $partido);

        $user = Auth::user();
        $torneosGestionadosIds = $user->admin ? Torneo::pluck('id') : $user->torneosGestionados->pluck('id');

        $validated = $request->validate([
            'jornada_id' => [
                'required',
                'exists:jornadas,id',
                function ($attribute, $value, $fail) use ($torneosGestionadosIds) {
                    $jornada = Jornada::find($value);
                    if (!$jornada || !$torneosGestionadosIds->contains($jornada->torneo_id)) {
                        $fail('La jornada seleccionada no pertenece a un torneo bajo tu gestión.');
                    }
                },
            ],
            'equipo_local_id' => 'required|exists:equipos,id|different:equipo_visitante_id',
            'equipo_visitante_id' => 'required|exists:equipos,id',
            'fecha_partido' => 'required|date',
            'goles_local' => 'nullable|integer|min:0',
            'goles_visitante' => 'nullable|integer|min:0',
            'estado' => 'required|in:programado,jugado,cancelado',
        ]);

        $partido->update($validated);

        if ($partido->estado === 'jugado') {
            $partido->actualizarEstadisticas();
        }

        return redirect()->route('admin.local.partidos.index')->with('success', 'Partido actualizado correctamente.');
    }

    public function destroy(Partido $partido)
    {
        $this->authorize('delete', $partido);

        $partido->delete();

        return redirect()->route('admin.local.partidos.index')->with('success', 'Partido eliminado correctamente.');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'partidos' => 'required|array',
            'partidos.*.id' => 'required|exists:partidos,id',
            'partidos.*.goles_local' => 'nullable|integer|min:0',
            'partidos.*.goles_visitante' => 'nullable|integer|min:0',
            'partidos.*.estado' => 'required|in:programado,jugado,cancelado',
        ]);

        foreach ($validated['partidos'] as $data) {
            $partido = Partido::findOrFail($data['id']);
            $this->authorize('update', $partido);

            $partido->update([
                'goles_local' => $data['goles_local'],
                'goles_visitante' => $data['goles_visitante'],
                'estado' => $data['estado'],
            ]);

            if ($partido->estado === 'jugado') {
                $partido->actualizarEstadisticas();
            }
        }

        return back()->with('success', 'Resultados actualizados correctamente.');
    }
}
