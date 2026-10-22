<?php

namespace App\Http\Controllers;

use App\Models\Jornada;
use App\Models\Torneo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLocalJornadaController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;
        $torneoId = $request->get('torneo_id');

        $query = Jornada::with('torneo');

        if (!$user->admin) {
            $torneosGestionadosIds = $torneos->pluck('id');
            $query->whereIn('torneo_id', $torneosGestionadosIds);
        }

        if ($torneoId) {
            $query->where('torneo_id', $torneoId);
        }

        $jornadas = $query->orderBy('orden')->paginate(10)->withQueryString();

        return view('admin-local.jornadas.index', compact('jornadas', 'torneos', 'torneoId'));
    }

    public function show(Jornada $jornada)
    {
        $this->authorize('view', $jornada);
        $jornada->load(['torneo', 'partidos.equipoLocal', 'partidos.equipoVisitante']);
        return view('admin-local.jornadas.show', compact('jornada'));
    }

    public function create()
    {
        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;

        return view('admin-local.jornadas.create', compact('torneos'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $torneosGestionadosIds = $user->admin ? Torneo::pluck('id') : $user->torneosGestionados->pluck('id');

        $validated = $request->validate([
            'torneo_id' => ['required', 'exists:torneos,id', function ($attribute, $value, $fail) use ($torneosGestionadosIds) {
                if (!$torneosGestionadosIds->contains($value)) {
                    $fail('El torneo seleccionado no es válido o no está bajo tu gestión.');
                }
            }],
            'nombre' => 'required|string|max:255',
            'orden' => 'nullable|integer',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'fecha_cierre_alineaciones' => 'nullable|date',
        ]);

        Jornada::create($validated);

        return redirect()->route('admin.local.jornadas.index')->with('success', 'Jornada creada correctamente.');
    }

    public function edit(Jornada $jornada)
    {
        $this->authorize('update', $jornada);

        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;

        return view('admin-local.jornadas.edit', compact('jornada', 'torneos'));
    }

    public function update(Request $request, Jornada $jornada)
    {
        $this->authorize('update', $jornada);

        $user = Auth::user();
        $torneosGestionadosIds = $user->admin ? Torneo::pluck('id') : $user->torneosGestionados->pluck('id');

        $validated = $request->validate([
            'torneo_id' => ['required', 'exists:torneos,id', function ($attribute, $value, $fail) use ($torneosGestionadosIds) {
                if (!$torneosGestionadosIds->contains($value)) {
                    $fail('El torneo seleccionado no es válido o no está bajo tu gestión.');
                }
            }],
            'nombre' => 'required|string|max:255',
            'orden' => 'nullable|integer',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'fecha_cierre_alineaciones' => 'nullable|date',
        ]);

        $jornada->update($validated);

        return redirect()->route('admin.local.jornadas.index')->with('success', 'Jornada actualizada correctamente.');
    }

    public function destroy(Jornada $jornada)
    {
        $this->authorize('delete', $jornada);

        $jornada->delete();

        return redirect()->route('admin.local.jornadas.index')->with('success', 'Jornada eliminada correctamente.');
    }
}
