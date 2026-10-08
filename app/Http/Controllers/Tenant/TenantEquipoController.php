<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Illuminate\Http\Request;

class TenantEquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::all();
        return view('tenant.equipos.index', compact('equipos'));
    }

    public function create()
    {
        return view('tenant.equipos.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        Equipo::create($request->all());
        return redirect()->route('tenant.equipos.index')->with('success', 'Equipo creado correctamente.');
    }

    public function edit(Equipo $equipo)
    {
        return view('tenant.equipos.edit', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $equipo->update($request->all());
        return redirect()->route('tenant.equipos.index')->with('success', 'Equipo actualizado.');
    }

    public function destroy(Equipo $equipo)
    {
        $equipo->delete();
        return redirect()->route('tenant.equipos.index')->with('success', 'Equipo eliminado.');
    }
}
