<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Jugador;
use App\Models\Equipo;
use Illuminate\Http\Request;

class TenantJugadorController extends Controller
{
    public function index()
    {
        $jugadores = Jugador::with('equipo')->get();
        return view('tenant.jugadores.index', compact('jugadores'));
    }

    public function create()
    {
        $equipos = Equipo::all();
        return view('tenant.jugadores.create', compact('equipos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'posicion' => 'required|string|max:50',
            'valor' => 'required|numeric',
            'equipo_id' => 'required|exists:equipos,id',
        ]);

        $jugador = Jugador::create([
            'nombre' => $validated['nombre'],
            'posicion' => $validated['posicion'],
            'precio' => $validated['valor'],
        ]);

        $jugador->equipos()->sync([$validated['equipo_id']]);

        return redirect()->route('tenant.jugadores.index')->with('success', 'Jugador creado correctamente.');
    }

    public function edit(Jugador $jugador)
    {
        $equipos = Equipo::all();
        return view('tenant.jugadores.edit', compact('jugador', 'equipos'));
    }

    public function update(Request $request, Jugador $jugador)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'posicion' => 'required|string|max:50',
            'valor' => 'required|numeric',
            'equipo_id' => 'required|exists:equipos,id',
        ]);

        $jugador->update([
            'nombre' => $validated['nombre'],
            'posicion' => $validated['posicion'],
            'precio' => $validated['valor'],
        ]);

        $jugador->equipos()->sync([$validated['equipo_id']]);

        return redirect()->route('tenant.jugadores.index')->with('success', 'Jugador actualizado correctamente.');
    }

    public function destroy(Jugador $jugador)
    {
        $jugador->delete();
        return redirect()->route('tenant.jugadores.index')->with('success', 'Jugador eliminado.');
    }
}
