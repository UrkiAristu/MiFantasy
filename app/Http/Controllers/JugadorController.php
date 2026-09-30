<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JugadorController extends Controller
{
    ///////////////////////////ADMIN////////////////////
    public function mostrarPaginaJugadores()
    {
        $jugadores = Jugador::all();
        return view('admin.jugadores', compact('jugadores'));
    }

    public function mostrarPaginaJugador($id)
    {
        $jugador = Jugador::with(['equipos', 'participaciones'])->findOrFail($id);
        $equiposDisponibles = Equipo::whereNotIn('id', $jugador->equipos->pluck('id'))->get();
        return view('admin.jugador', compact('jugador', 'equiposDisponibles'));
    }

    public function crearJugador(Request $request)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'apellido1' => 'required|string|max:255',
                'apellido2' => 'required|string|max:255',
                'fecha_nacimiento' => 'required|date',
                'posicion' => 'nullable|string|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            [
                'nombre.required' => 'El nombre del jugador es obligatorio.',
                'nombre.string' => 'El nombre del jugador debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del jugador no puede tener más de 255 caracteres.',
                'apellido1.required' => 'El primer apellido del jugador es obligatorio.',
                'apellido1.string' => 'El primer apellido del jugador debe ser una cadena de texto.',
                'apellido1.max' => 'El primer apellido del jugador no puede tener más de 255 caracteres.',
                'apellido2.required' => 'El segundo apellido del jugador es obligatorio.',
                'apellido2.string' => 'El segundo apellido del jugador debe ser una cadena de texto.',
                'apellido2.max' => 'El segundo apellido del jugador no puede tener más de 255 caracteres.',
                'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
                'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
                'posicion.string' => 'La posición del jugador debe ser una cadena de texto.',
                'posicion.max' => 'La posición del jugador no puede tener más de 255 caracteres.',
                'foto.image' => 'La foto debe ser una imagen válida (jpeg, png, jpg, gif).',
                'foto.max' => 'La foto no puede tener más de 2 MB.',
            ]
        );

        $jugador = new Jugador();
        $jugador->nombre = $validated['nombre'];
        $jugador->apellido1 = $validated['apellido1'];
        $jugador->apellido2 = $validated['apellido2'];
        $jugador->fecha_nacimiento = $validated['fecha_nacimiento'];
        $jugador->posicion = $validated['posicion'];

        if ($request->hasFile('foto')) {
            $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre'] . '_' . $validated['apellido1'] . '_' . $validated['apellido2']);
            $timestamp = time();
            $extension = $request->file('foto')->extension();
            $fotoFileName = "foto_{$nombreJugador}_{$timestamp}.{$extension}";

            $path = $request->file('foto')->storeAs('jugadores_fotos', $fotoFileName, 'public');
            $jugador->foto = $path;
        } else {
            $jugador->foto = null;
        }
        $jugador->save();

        return redirect('/admin/jugadores')->with('success', 'Jugador creado exitosamente.');
    }

    public function eliminarJugador($id)
    {
        $jugador = Jugador::findOrFail($id);

        if ($jugador->foto && Storage::disk('public')->exists($jugador->foto)) {
            Storage::disk('public')->delete($jugador->foto);
        }

        $jugador->delete();
        return redirect('/admin/jugadores')->with('success', 'Jugador eliminado correctamente.');
    }

    public function editarJugador(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'apellido1' => 'required|string|max:255',
                'apellido2' => 'required|string|max:255',
                'fecha_nacimiento' => 'required|date',
                'posicion' => 'nullable|string|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            [
                'nombre.required' => 'El nombre del jugador es obligatorio.',
                'nombre.string' => 'El nombre del jugador debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del jugador no puede tener más de 255 caracteres.',
                'apellido1.required' => 'El primer apellido del jugador es obligatorio.',
                'apellido1.string' => 'El primer apellido del jugador debe ser una cadena de texto.',
                'apellido1.max' => 'El primer apellido del jugador no puede tener más de 255 caracteres.',
                'apellido2.required' => 'El segundo apellido del jugador es obligatorio.',
                'apellido2.string' => 'El segundo apellido del jugador debe ser una cadena de texto.',
                'apellido2.max' => 'El segundo apellido del jugador no puede tener más de 255 caracteres.',
                'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
                'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
                'posicion.string' => 'La posición del jugador debe ser una cadena de texto.',
                'posicion.max' => 'La posición del jugador no puede tener más de 255 caracteres.',
                'foto.image' => 'La foto debe ser una imagen válida (jpeg, png, jpg, gif).',
                'foto.max' => 'La foto no puede tener más de 2 MB.',
            ]
        );

        $jugador = Jugador::findOrFail($id);
        $jugador->nombre = $validated['nombre'];
        $jugador->apellido1 = $validated['apellido1'];
        $jugador->apellido2 = $validated['apellido2'];
        $jugador->fecha_nacimiento = $validated['fecha_nacimiento'];
        $jugador->posicion = $validated['posicion'];

        if ($request->has('eliminar_foto') && $request->eliminar_foto) {
            if ($jugador->foto && Storage::disk('public')->exists($jugador->foto)) {
                Storage::disk('public')->delete($jugador->foto);
            }
            $jugador->foto = null;
        }

        if ($request->hasFile('foto')) {
            if ($jugador->foto && Storage::disk('public')->exists($jugador->foto)) {
                Storage::disk('public')->delete($jugador->foto);
            }
            $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre . '_' . $request->apellido1 . '_' . $request->apellido2);
            $timestamp = time();
            $extension = $request->file('foto')->extension();
            $fotoFileName = "foto_{$nombreJugador}_{$timestamp}.{$extension}";

            $path = $request->file('foto')->storeAs('jugadores_fotos', $fotoFileName, 'public');
            $jugador->foto = $path;
        }

        $jugador->save();
        return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador actualizado correctamente.');
    }

    public function agregarAEquipoJugador(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'equipo_id' => 'required|exists:equipos,id',
            ],
            [
                'equipo_id.required' => 'El equipo es obligatorio.',
                'equipo_id.exists' => 'El equipo seleccionado no existe.',
            ]
        );

        $jugador = Jugador::findOrFail($id);
        if ($jugador->equipos()->where('equipos.id', $validated['equipo_id'])->exists()) {
            return redirect("/admin/jugadores/{$id}")
                ->withErrors(['El jugador ya está en este equipo.']);
        }

        $equipo = Equipo::findOrFail($validated['equipo_id']);
        $equipo->jugadores()->attach($jugador->id);
        return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador agregado al equipo correctamente.');
    }

    public function eliminarDeEquipoJugador($id, $equipoId)
    {
        $jugador = Jugador::findOrFail($id);
        if (!$jugador->equipos()->where('equipo_id', $equipoId)->exists()) {
            return redirect("/admin/jugadores/{$id}")->withErrors(['El equipo no está inscrito en este torneo.']);
        }

        $jugador->equipos()->detach($equipoId);
        return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador eliminado del equipo correctamente.');
    }

    public function crearEquipoConJugador(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'logo' => 'nullable|image|max:2048',
            ],
            [
                'nombre.required' => 'El nombre del equipo es obligatorio.',
                'nombre.string' => 'El nombre del equipo debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del equipo no puede tener más de 255 caracteres.',
                'logo.image' => 'El logo debe ser una imagen válida.',
                'logo.max' => 'El logo no puede tener más de 2 MB.',
            ]
        );

        $jugador = Jugador::findOrFail($id);
        $equipo = new Equipo();
        $equipo->nombre = $validated['nombre'];

        if ($request->hasFile('logo')) {
            $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->extension();
            $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";

            $path = $request->file('logo')->storeAs('equipos_logos', $logoFileName, 'public');
            $equipo->logo = $path;
        } else {
            $equipo->logo = null;
        }
        $equipo->save();

        $jugador->equipos()->attach($equipo->id);
        return redirect("/admin/jugadores/{$id}")->with('success', 'Equipo creado e inscrito el jugador correctamente.');
    }

    ///////////////////////////USER////////////////////
    public function info($idJugador, $idTorneo)
    {
        $jugador = Jugador::findOrFail($idJugador);
        $edad = Carbon::parse($jugador->fecha_nacimiento)->age;
        $estadisticas = $jugador->resumenEstadisticasEnTorneo($idTorneo);
        return response()->json([
            'nombre' => $jugador->nombre,
            'apellido1' => $jugador->apellido1,
            'apellido2' => $jugador->apellido2,
            'foto' => $jugador->foto ? asset($jugador->foto) : null,
            'equipo' => $jugador->equipoEnTorneo($idTorneo)->nombre ?? '',
            'posicion' => $jugador->posicion,
            'edad' => $edad,
            'partidos' => $estadisticas['partidos_jugados'],
            'goles' => $estadisticas['goles'],
            'asistencias' => $estadisticas['asistencias'],
            'paradas' => $estadisticas['paradas'],
            'faltas' => $estadisticas['faltas'],
            'tarjetas_amarillas' => $estadisticas['amarillas'],
            'tarjetas_rojas' => $estadisticas['rojas'],
            'puntos' => $estadisticas['puntos'],
        ]);
    }
}

