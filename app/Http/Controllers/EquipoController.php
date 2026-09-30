<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    public function mostrarPaginaEquipos()
    {
        $equipos = Equipo::all();

        return view('admin.equipos', compact('equipos'));
    }

    public function mostrarPaginaEquipo($id)
    {
        $equipo = Equipo::with(['torneos', 'jugadores'])->findOrFail($id);
        $torneosDisponibles = Torneo::whereNotIn('id', $equipo->torneos->pluck('id'))->get();
        $jugadoresDisponibles = Jugador::whereNotIn('id', $equipo->jugadores->pluck('id'))->get();

        return view('admin.equipo', compact('equipo', 'torneosDisponibles', 'jugadoresDisponibles'));
    }

    public function crearEquipo(Request $request)
    {
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            [
                'nombre.required' => 'El nombre del equipo es obligatorio.',
                'nombre.string' => 'El nombre del equipo debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del equipo no puede tener más de 255 caracteres.',
                'logo.image' => 'El logo debe ser una imagen válida (jpeg, png, jpg, gif).',
                'logo.max' => 'El logo no puede tener más de 2 MB.',
            ]
        );

        $equipo = new Equipo;
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

        return redirect('/admin/equipos')->with('success', 'Equipo creado correctamente.');
    }

    public function eliminarEquipo($id)
    {
        $equipo = Equipo::findOrFail($id);

        if ($equipo->logo && Storage::disk('public')->exists($equipo->logo)) {
            Storage::disk('public')->delete($equipo->logo);
        }

        $equipo->delete();

        return redirect('/admin/equipos')->with('success', 'Equipo eliminado correctamente.');
    }

    public function editarEquipo(Request $request, $id)
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
                'logo.image' => 'El logo debe ser una imagen válida (jpeg, png, jpg, gif).',
                'logo.max' => 'El logo no puede tener más de 2 MB.',
            ]
        );

        $equipo = Equipo::findOrFail($id);
        $equipo->nombre = $validated['nombre'];

        if ($request->has('eliminar_logo') && $request->eliminar_logo) {
            if ($equipo->logo && Storage::disk('public')->exists($equipo->logo)) {
                Storage::disk('public')->delete($equipo->logo);
            }
            $equipo->logo = null;
        }

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

        return redirect("/admin/equipos/{$id}")->with('success', 'Equipo actualizado correctamente.');
    }

    public function inscribirATorneoEquipo(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'torneo_id' => 'required|exists:torneos,id',
            ],
            [
                'torneo_id.required' => 'El torneo es obligatorio.',
                'torneo_id.exists' => 'El torneo seleccionado no existe.',
            ]
        );

        $equipo = Equipo::findOrFail($id);

        if ($equipo->torneos()->where('torneos.id', $validated['torneo_id'])->exists()) {
            return redirect("/admin/equipos/{$id}")
                ->withErrors(['El equipo ya está inscrito en este torneo.']);
        }

        $torneo = Torneo::findOrFail($validated['torneo_id']);
        $equipo->torneos()->attach($torneo->id);

        return redirect("/admin/equipos/{$id}")->with('success', 'Equipo inscrito al torneo correctamente.');
    }

    public function eliminarDeTorneoEquipo($id, $torneoId)
    {
        $equipo = Equipo::findOrFail($id);

        if (! $equipo->torneos()->where('torneo_id', $torneoId)->exists()) {
            return redirect("/admin/equipos/{$id}")->withErrors(['El equipo no está inscrito en este torneo.']);
        }

        $equipo->torneos()->detach($torneoId);

        return redirect("/admin/equipos/{$id}")->with('success', 'Equipo eliminado del torneo correctamente.');
    }

    public function crearTorneoConEquipo(Request $request, $id)
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
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'estado' => 'required|boolean',
                'jugadores_por_equipo' => 'required|integer|min:1',
                'usa_posiciones' => 'nullable|boolean',
            ],
            [
                'nombre.required' => 'El nombre del torneo es obligatorio.',
                'nombre.string' => 'El nombre del torneo debe ser una cadena de texto.',
                'nombre.max' => 'El nombre del torneo no puede tener más de 255 caracteres.',
                'descripcion.string' => 'La descripción debe ser una cadena de texto.',
                'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
                'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
                'fecha_fin.required' => 'La fecha de fin es obligatoria.',
                'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                'logo.image' => 'El logo debe ser una imagen válida (jpeg, png, jpg, gif).',
                'logo.mimes' => 'El logo debe ser un archivo de imagen válido (jpeg, png, jpg, gif).',
                'logo.max' => 'El logo no puede tener más de 2 MB.',
                'estado.required' => 'El estado es obligatorio.',
                'estado.boolean' => 'El estado debe ser verdadero o falso.',
                'jugadores_por_equipo.required' => 'El número de jugadores por equipo es obligatorio.',
                'jugadores_por_equipo.integer' => 'El número de jugadores por equipo debe ser un número entero.',
                'jugadores_por_equipo.min' => 'Debe haber al menos 1 jugador por equipo.',
                'usa_posiciones.boolean' => 'El campo "Usar posiciones" debe ser verdadero o falso.',
            ]
        );

        $equipo = Equipo::findOrFail($id);

        $torneo = new Torneo;
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

        $equipo->torneos()->attach($torneo->id);

        return redirect("/admin/equipos/{$id}")->with('success', 'Torneo creado e inscrito al equipo correctamente.');
    }

    public function agregarJugadorAEquipo(Request $request, $id)
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

        $equipo = Equipo::findOrFail($id);

        if ($equipo->jugadores()->where('jugadores.id', $validated['jugador_id'])->exists()) {
            return redirect("/admin/equipos/{$id}")
                ->withErrors(['El jugador ya está en este equipo.']);
        }

        $jugador = Jugador::findOrFail($validated['jugador_id']);
        $equipo->jugadores()->attach($jugador->id);

        return redirect("/admin/equipos/{$id}")->with('success', 'Jugador agregado al equipo correctamente.');
    }

    public function eliminarJugadorDeEquipo($id, $jugadorId)
    {
        $equipo = Equipo::findOrFail($id);

        if (! $equipo->jugadores()->where('jugador_id', $jugadorId)->exists()) {
            return redirect("/admin/equipos/{$id}")->withErrors(['El jugador no está en este equipo.']);
        }

        $equipo->jugadores()->detach($jugadorId);

        return redirect("/admin/equipos/{$id}")->with('success', 'Jugador eliminado del equipo correctamente.');
    }

    public function crearJugadorEnEquipo(Request $request, $id)
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

        $equipo = Equipo::findOrFail($id);

        $jugador = new Jugador;
        $jugador->nombre = $validated['nombre'];
        $jugador->apellido1 = $validated['apellido1'];
        $jugador->apellido2 = $validated['apellido2'];
        $jugador->fecha_nacimiento = $validated['fecha_nacimiento'];
        $jugador->posicion = $validated['posicion'] ?? null;

        if ($request->hasFile('foto')) {
            $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre'].'_'.$validated['apellido1'].'_'.$validated['apellido2']);
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

        return redirect("/admin/equipos/{$id}")->with('success', 'Jugador creado e inscrito en el equipo correctamente.');
    }
}
