<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TorneoController extends Controller
{
    //////////////////ADMIN///////////////
    public function mostrarPaginaTorneos()
    {
        $torneos = Torneo::all();
        return view('admin.torneos', compact('torneos'));
    }

    public function mostrarPaginaTorneo($id)
    {
        $torneo = Torneo::with(['equipos', 'jugadores'])->findOrFail($id);
        $equiposDisponibles = Equipo::whereNotIn('id', $torneo->equipos->pluck('id'))->get();
        return view('admin.torneo', compact('torneo', 'equiposDisponibles'));
    }

    public function crearTorneo(Request $request)
    {
        $request->merge([
            'usa_posiciones' => $request->has('usa_posiciones') ? 1 : 0,
        ]);

        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'estado' => 'boolean',
                'logo' => 'nullable|image|max:2048',
                'jugadores_por_equipo' => 'required|integer|min:1',
                'usa_posiciones' => 'nullable|boolean',
            ],
            [
                'nombre.required' => 'El nombre del torneo es obligatorio.',
                'nombre.string' => 'El nombre del torneo debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del torneo no puede tener más de 255 caracteres.',
                'descripcion.string' => 'La descripción del torneo debe ser una cadena de texto.',
                'descripcion.max' => 'La descripción del torneo no puede tener más de 1000 caracteres.',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
                'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
                'fecha_fin.required' => 'La fecha de fin es obligatoria.',
                'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                'estado.boolean' => 'El campo estado debe ser verdadero o falso.',
                'logo.image' => 'El logo debe ser una imagen válida.',
                'logo.max' => 'El logo no puede tener más de 2 MB.',
                'jugadores_por_equipo.required' => 'El número de jugadores por equipo es obligatorio.',
                'jugadores_por_equipo.integer' => 'El número de jugadores por equipo debe ser un número entero.',
                'jugadores_por_equipo.min' => 'Debe haber al menos 1 jugador por equipo.',
                'usa_posiciones.boolean' => 'El campo "Usar posiciones" debe ser verdadero o falso.',
            ]
        );

        $torneo = new Torneo();
        $torneo->nombre = $validated['nombre'];
        $torneo->descripcion = $validated['descripcion'] ?? null;
        $torneo->fecha_inicio = $validated['fecha_inicio'];
        $torneo->fecha_fin = $validated['fecha_fin'];
        $torneo->estado = $validated['estado'] ?? false;
        $torneo->jugadores_por_equipo = $validated['jugadores_por_equipo'];
        $torneo->usa_posiciones = $validated['usa_posiciones'] ?? 0;

        if ($request->hasFile('logo')) {
            $nombreTorneo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->extension();
            $logoFileName = "logo_{$nombreTorneo}_{$timestamp}.{$extension}";

            $path = $request->file('logo')->storeAs('torneos_logos', $logoFileName, 'public');
            $torneo->logo = $path;
        } else {
            $torneo->logo = null;
        }

        $torneo->save();

        return redirect('/admin/torneos')->with('success', 'Torneo creado correctamente.');
    }

    public function eliminarTorneo($id)
    {
        $torneo = Torneo::findOrFail($id);

        if ($torneo->logo && Storage::disk('public')->exists($torneo->logo)) {
            Storage::disk('public')->delete($torneo->logo);
        }

        $torneo->delete();
        return redirect('/admin/torneos')->with('success', 'Torneo eliminado correctamente.');
    }

    public function editarTorneo(Request $request, $id)
    {
        $request->merge([
            'usa_posiciones' => $request->has('usa_posiciones') ? 1 : 0,
        ]);

        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'estado' => 'boolean',
                'logo' => 'nullable|image|max:2048',
                'jugadores_por_equipo' => 'required|integer|min:1',
                'usa_posiciones' => 'nullable|boolean',
            ],
            [
                'nombre.required' => 'El nombre del torneo es obligatorio.',
                'nombre.string' => 'El nombre del torneo debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del torneo no puede tener más de 255 caracteres.',
                'descripcion.string' => 'La descripción del torneo debe ser una cadena de texto.',
                'descripcion.max' => 'La descripción del torneo no puede tener más de 1000 caracteres.',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
                'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
                'fecha_fin.required' => 'La fecha de fin es obligatoria.',
                'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                'estado.boolean' => 'El campo estado debe ser verdadero o falso.',
                'logo.image' => 'El logo debe ser una imagen válida.',
                'logo.max' => 'El logo no puede tener más de 2 MB.',
                'jugadores_por_equipo.required' => 'El número de jugadores por equipo es obligatorio.',
                'jugadores_por_equipo.integer' => 'El número de jugadores por equipo debe ser un número entero.',
                'jugadores_por_equipo.min' => 'Debe haber al menos 1 jugador por equipo.',
                'usa_posiciones.boolean' => 'El campo "Usar posiciones" debe ser verdadero o falso.',
            ]
        );

        $torneo = Torneo::findOrFail($id);
        $torneo->nombre = $validated['nombre'];
        $torneo->descripcion = $validated['descripcion'] ?? null;
        $torneo->fecha_inicio = $validated['fecha_inicio'];
        $torneo->fecha_fin = $validated['fecha_fin'];
        $torneo->estado = $validated['estado'] ?? false;
        $torneo->jugadores_por_equipo = $validated['jugadores_por_equipo'];
        $torneo->usa_posiciones = $validated['usa_posiciones'] ?? 0;

        if ($request->has('eliminar_logo') && $request->eliminar_logo) {
            if ($torneo->logo && Storage::disk('public')->exists($torneo->logo)) {
                Storage::disk('public')->delete($torneo->logo);
            }
            $torneo->logo = null;
        }

        if ($request->hasFile('logo')) {
            if ($torneo->logo && Storage::disk('public')->exists($torneo->logo)) {
                Storage::disk('public')->delete($torneo->logo);
            }
            $nombreTorneo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->extension();
            $logoFileName = "logo_{$nombreTorneo}_{$timestamp}.{$extension}";

            $path = $request->file('logo')->storeAs('torneos_logos', $logoFileName, 'public');
            $torneo->logo = $path;
        }

        $torneo->save();

        return redirect("/admin/torneos/{$id}")->with('success', 'Torneo actualizado correctamente.');
    }

    public function agregarEquipoATorneo(Request $request, $id)
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

        $torneo = Torneo::findOrFail($id);

        if ($torneo->equipos()->where('equipos.id', $validated['equipo_id'])->exists()) {
            return redirect("/admin/torneos/{$id}")
                ->withErrors(['El equipo ya está inscrito en este torneo.']);
        }

        $equipo = Equipo::findOrFail($validated['equipo_id']);
        $torneo->equipos()->attach($equipo->id);

        return redirect("/admin/torneos/{$id}")->with('success', 'Equipo agregado al torneo correctamente.');
    }

    public function eliminarEquipoDeTorneo($id, $equipoId)
    {
        $torneo = Torneo::findOrFail($id);

        if (!$torneo->equipos()->where('equipo_id', $equipoId)->exists()) {
            return redirect("/admin/torneos/{$id}")->withErrors(['El equipo no está inscrito en este torneo.']);
        }

        $torneo->equipos()->detach($equipoId);
        return redirect("/admin/torneos/{$id}")->with('success', 'Equipo eliminado del torneo correctamente.');
    }

    public function crearEquipoEnTorneo(Request $request, $id)
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

        $torneo = Torneo::findOrFail($id);

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
        $torneo->equipos()->attach($equipo->id);

        return redirect("/admin/torneos/{$id}")->with('success', 'Equipo creado y agregado al torneo correctamente.');
    }

    public function mostrarPaginaJugadoresDeEquipoEnTorneo($id, $equipoId)
    {
        $torneo = Torneo::findOrFail($id);
        $equipo = $torneo->equipos()->findOrFail($equipoId);

        $jugadores = Jugador::whereHas('participaciones', function ($q) use ($id, $equipoId) {
            $q->where('torneo_id', $id)
                ->where('equipo_id', $equipoId);
        })->get();

        $todosJugadoresDelEquipo = $equipo->jugadores;
        $jugadoresDisponibles = $todosJugadoresDelEquipo->diff($jugadores);

        return view('admin.jugadores_equipo_torneo', compact('torneo', 'equipo', 'jugadores', 'jugadoresDisponibles'));
    }

    public function agregarJugadorAEquipoEnTorneo(Request $request, $id, $equipoId)
    {
        $validated = $request->validate(
            [
                'jugador_id' => 'required|exists:jugadores,id',
            ],
            [
                'jugador_id.required' => 'El jugador es obligatorio.',
                'jugador_id.exists' => 'El jugador seleccionado no existe.',
            ]
        );

        $torneo = Torneo::findOrFail($id);
        $equipo = $torneo->equipos()->findOrFail($equipoId);
        $jugador = Jugador::findOrFail($validated['jugador_id']);

        $equipo->jugadoresEnTorneos()->attach($jugador->id, ['torneo_id' => $torneo->id]);
        return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")->with('success', 'Jugador agregado al equipo en el torneo correctamente.');
    }

    public function eliminarJugadorDeEquipoEnTorneo($id, $equipoId, $jugadorId)
    {
        $torneo = Torneo::findOrFail($id);
        $equipo = $torneo->equipos()->findOrFail($equipoId);

        if (!$equipo->jugadoresEnTorneos()
            ->wherePivot('torneo_id', $torneo->id)
            ->wherePivot('jugador_id', $jugadorId)
            ->exists()
        ) {
            return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")
                ->withErrors(['El jugador no está inscrito en este equipo en el torneo.']);
        }

        $equipo->jugadoresEnTorneos()
            ->wherePivot('torneo_id', $torneo->id)
            ->detach($jugadorId);

        return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")
            ->with('success', 'Jugador eliminado del equipo en el torneo correctamente.');
    }

    public function crearJugadorEnEquipoEnTorneo(Request $request, $id, $equipoId)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'apellido1' => 'required|string|max:255',
                'apellido2' => 'required|string|max:255',
                'fecha_nacimiento' => 'required|date',
                'posicion' => 'nullable|string|max:50',
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
                'fecha_nacimiento.required' => 'La fecha de nacimiento del jugador es obligatoria.',
                'fecha_nacimiento.date' => 'La fecha de nacimiento del jugador debe ser una fecha válida.',
                'posicion.required' => 'La posición del jugador es obligatoria.',
                'posicion.string' => 'La posición del jugador debe ser una cadena de texto.',
                'posicion.max' => 'La posición del jugador no puede tener más de 50 caracteres.',
                'foto.image' => 'La foto debe ser una imagen válida (jpeg, png, jpg, gif).',
                'foto.mimes' => 'La foto debe ser un archivo de imagen válido (jpeg, png, jpg, gif).',
                'foto.max' => 'La foto no puede tener más de 2 MB.',
            ]
        );

        $torneo = Torneo::findOrFail($id);
        $equipo = $torneo->equipos()->findOrFail($equipoId);

        if (!$torneo->equipos()->where('equipo_id', $equipoId)->exists()) {
            return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")
                ->withErrors(['El equipo no está inscrito en este torneo.']);
        }

        $jugador = new Jugador();
        $jugador->nombre = $validated['nombre'];
        $jugador->apellido1 = $validated['apellido1'];
        $jugador->apellido2 = $validated['apellido2'];
        $jugador->fecha_nacimiento = $validated['fecha_nacimiento'];
        $jugador->posicion = $validated['posicion'] ?? null;

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

        $equipo->jugadores()->attach($jugador->id);
        $equipo->jugadoresEnTorneos()->attach($jugador->id, ['torneo_id' => $torneo->id]);

        return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")
            ->with('success', 'Jugador creado e inscrito en el equipo del torneo correctamente.');
    }

    ///////////////////////////USER////////////////////
    public function mostrarPaginaTorneosUser()
    {
        $torneos = Torneo::activos()->with('equipos')->get();
        return view('user.torneos', compact('torneos'));
    }
}
