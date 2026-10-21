<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipoRequest;
use App\Http\Requests\UpdateEquipoRequest;
use App\Models\Equipo;
use App\Models\Torneo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminLocalEquipoController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;
        $torneoId = $request->get('torneo_id');

        $query = Equipo::with(['torneos', 'jugadores']);

        if (!$user->admin) {
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

    public function create()
    {
        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;

        return view('admin-local.equipos.create', compact('torneos'));
    }

    public function store(StoreEquipoRequest $request)
    {
        $validated = $request->validated();

        $equipo = new Equipo();
        $equipo->nombre = $validated['nombre'];

        if ($request->hasFile('logo')) {
            $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->extension();
            $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";
            $path = $request->file('logo')->storeAs('equipos_logos', $logoFileName, 'public');
            $equipo->logo = $path;
        }

        $equipo->save();

        if (!empty($validated['torneos'])) {
            $equipo->torneos()->sync($validated['torneos']);
        }

        return redirect()->route('admin.local.equipos.index')->with('success', 'Equipo creado correctamente.');
    }

    public function edit(Equipo $equipo)
    {
        $this->authorize('update', $equipo);

        $user = Auth::user();
        $torneos = $user->admin ? Torneo::all() : $user->torneosGestionados;
        $equipoTorneosIds = $equipo->torneos->pluck('id')->toArray();

        return view('admin-local.equipos.edit', compact('equipo', 'torneos', 'equipoTorneosIds'));
    }

    public function update(UpdateEquipoRequest $request, Equipo $equipo)
    {
        $this->authorize('update', $equipo);

        $validated = $request->validated();
        $equipo->nombre = $validated['nombre'];

        if ($request->hasFile('logo')) {
            if ($equipo->logo && Storage::disk('public')->exists($equipo->logo)) {
                Storage::disk('public')->delete($equipo->logo);
            }
            $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->extension();
            $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";
            $path = $request->file('logo')->storeAs('equipos_logos', $logoFileName, 'public');
            $equipo->logo = $path;
        }

        $equipo->save();

        if (isset($validated['torneos'])) {
            $equipo->torneos()->sync($validated['torneos']);
        }

        return redirect()->route('admin.local.equipos.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipo $equipo)
    {
        $this->authorize('delete', $equipo);

        if ($equipo->logo && Storage::disk('public')->exists($equipo->logo)) {
            Storage::disk('public')->delete($equipo->logo);
        }

        $equipo->torneos()->detach();
        $equipo->delete();

        return redirect()->route('admin.local.equipos.index')->with('success', 'Equipo eliminado correctamente.');
    }
}
