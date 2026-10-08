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
        $request->validate([
            'nombre' => 'required|string|max:255',
            'posicion' => 'required|string|max:50',
            'valor' => 'required|numeric',
            'equipo_id' => 'required|exists:equipos,id',
        ]);
        Jugador::create($request->all());
        return redirect()->route('tenant.jugadores.index')->with('success', 'Jugador creado.');
    }

    public function edit(Jugador $jugador)
    {
        $equipos = Equipo::all();
        return view('tenant.jugadores.edit', compact('jugador', 'equipos'));
    }

    public function update(Request $request, Jugador $jugador)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'posicion' => 'required|string|max:50',
            'valor' => 'required|numeric',
            'equipo_id' => 'required|exists:equipos,id',
        ]);
        $jugador->update($request->all());
        return redirect()->route('tenant.jugadores.index')->with('success', 'Jugador actualizado.');
    }

    public function destroy(Jugador $jugador)
    {
        $jugador->delete();
        return redirect()->route('tenant.jugadores.index')->with('success', 'Jugador eliminado.');
    }
}
