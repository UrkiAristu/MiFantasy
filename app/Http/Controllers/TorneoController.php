<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TorneoController extends Controller
{
    //////////////////ADMIN///////////////
    public function mostrarPaginaTorneos()
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $torneos = Torneo::all();
        // Retornar la vista con los datos de los torneos
        return view('admin.torneos', compact('torneos'));
        
    }

    public function mostrarPaginaTorneo($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Buscar el torneo por ID
        $torneo = Torneo::with(['equipos','jugadores'])->findOrFail($id);
        // Obtener equipos disponibles (no inscritos en el torneo)
        $equiposDisponibles = Equipo::whereNotIn('id', $torneo->equipos->pluck('id'))->get();
        // Retornar la vista con los datos del torneo
        return view('admin.torneo', compact('torneo', 'equiposDisponibles'));
    }
    public function crearTorneo(Request $request)
    {

        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $request->merge([
            'usa_posiciones' => $request->has('usa_posiciones') ? 1 : 0,
        ]);
        // Validar los datos del formulario
        $validated =$request->validate(
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
        // Crear un nuevo torneo
        $torneo = new Torneo();
        $torneo->nombre = $validated['nombre'];
        $torneo->descripcion = $validated['descripcion'] ?? null;
        $torneo->fecha_inicio = $validated['fecha_inicio'];
        $torneo->fecha_fin = $validated['fecha_fin'];
        $torneo->estado = $validated['estado'] ?? false;
        $torneo->jugadores_por_equipo = $validated['jugadores_por_equipo'];
        $torneo->usa_posiciones = $validated['usa_posiciones'] ?? 0;
        // Logo
        if ($request->hasFile('logo')) {
            $nombreTorneo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->getClientOriginalExtension();
            $logoFileName = "logo_{$nombreTorneo}_{$timestamp}.{$extension}";

            // Guarda directo en /public/torneos_logos
            $request->file('logo')->move(public_path('torneos_logos'), $logoFileName);
            // Guarda solo la ruta relativa para mostrarla
            $torneo->logo = 'torneos_logos/' . $logoFileName;
        } else {
            $torneo->logo = null; // Si no se subió un logo, establecerlo como nulo
        }
        $torneo->save();

        return redirect('/admin/torneos')->with('success', 'Torneo creado correctamente.');
    }

    public function eliminarTorneo($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $torneo = Torneo::findOrFail($id);
        // Eliminar el logo del torneo si existe
        if ($torneo->logo && file_exists(public_path($torneo->logo))) {
            unlink(public_path($torneo->logo));
        }
        // Eliminar el torneo de la base de datos
        $torneo->delete();
        return redirect('/admin/torneos')->with('success', 'Torneo eliminado correctamente.');
    }

    public function editarTorneo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/admin/torneos')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $request->merge([
            'usa_posiciones' => $request->has('usa_posiciones') ? 1 : 0,
        ]);
        // Validar los datos del formulario
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

        // Buscar el torneo por ID
        $torneo = Torneo::findOrFail($id);
        if ($torneo) {
            $torneo->nombre = $validated['nombre'];
            $torneo->descripcion = $validated['descripcion'] ?? null;
            $torneo->fecha_inicio = $validated['fecha_inicio'];
            $torneo->fecha_fin = $validated['fecha_fin'];
            $torneo->estado = $validated['estado'] ?? false;
            $torneo->jugadores_por_equipo = $validated['jugadores_por_equipo'];
            $torneo->usa_posiciones = $validated['usa_posiciones'] ?? 0;
            if ($request->has('eliminar_logo') && $validated['eliminar_logo']) {
                // Eliminar el logo del torneo si se ha marcado la opción
                if ($torneo->logo && file_exists(public_path($torneo->logo))) {
                    unlink(public_path($torneo->logo));
                }
                $torneo->logo = null; // Establecer el logo como nulo
            }
            if ($request->hasFile('logo')) {
                // Eliminar el logo anterior si existe
                if ($torneo->logo && file_exists(public_path($torneo->logo))) {
                    unlink(public_path($torneo->logo));
                }
                $nombreTorneo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
                $timestamp = time();
                $extension = $request->file('logo')->getClientOriginalExtension();
                $logoFileName = "logo_{$nombreTorneo}_{$timestamp}.{$extension}";
                // Guarda directo en /public/torneos_logos
                $request->file('logo')->move(public_path('torneos_logos'), $logoFileName);
                $torneo->logo = "torneos_logos/{$logoFileName}";
            }
        }

        // Guardar los cambios en la base de datos
        $torneo->save();

        return redirect("/admin/torneos/{$id}")->with('success', 'Torneo actualizado correctamente.');
    }

    public function agregarEquipoATorneo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'equipo_id' => 'required|exists:equipos,id', // Asegurarse de que el equipo exista
            ],
            [
                'equipo_id.required' => 'El equipo es obligatorio.',
                'equipo_id.exists' => 'El equipo seleccionado no existe.',
            ]
        );

        // Buscar el torneo por ID
        $torneo = Torneo::findOrFail($id);

        // Comprobar que el equipo no esté ya inscrito en el torneo
        if ($torneo->equipos()->where('equipos.id', $validated['equipo_id'])->exists()) {
            return redirect("/admin/torneos/{$id}")
                ->withErrors(['El equipo ya está inscrito en este torneo.']);
        }

        // Buscar el equipo por ID
        $equipo = Equipo::findOrFail($validated['equipo_id']);
        // Agregar el equipo al torneo
        $torneo->equipos()->attach($equipo->id);

        return redirect("/admin/torneos/{$id}")->with('success', 'Equipo agregado al torneo correctamente.');
        
    }

    public function eliminarEquipoDeTorneo($id, $equipoId)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Buscar el torneo por ID
        $torneo = Torneo::findOrFail($id);
        // Verificar si el equipo está inscrito en el torneo
        if (!$torneo->equipos()->where('equipo_id', $equipoId)->exists()) {
            return redirect("/admin/torneos/{$id}")->withErrors(['El equipo no está inscrito en este torneo.']);
        }
        // Eliminar el equipo del torneo
        $torneo->equipos()->detach($equipoId);
        return redirect("/admin/torneos/{$id}")->with('success', 'Equipo eliminado del torneo correctamente.');
    }

    public function crearEquipoEnTorneo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'logo' => 'nullable|image|max:2048', // Asumiendo que hay un campo para subir un logo
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

        // Crear un nuevo equipo
        $equipo = new Equipo();
        $equipo->nombre = $validated['nombre'];
        if ($request->hasFile('logo')) {
            $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->getClientOriginalExtension();
            $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";
            // Guarda directo en /public/equipos_logos
            $request->file('logo')->move(public_path('equipos_logos'), $logoFileName);
            $equipo->logo = 'equipos_logos/' . $logoFileName;
        } else {
            $equipo->logo = null; // Si no se subió un logo, establecerlo como nulo
        }
        $equipo->save();

        // Agregar el equipo al torneo
        $torneo->equipos()->attach($equipo->id);
        return redirect("/admin/torneos/{$id}")->with('success', 'Equipo creado y agregado al torneo correctamente.');
    }

    public function mostrarPaginaJugadoresDeEquipoEnTorneo($id, $equipoId)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Buscar el torneo por ID
        $torneo = Torneo::findOrFail($id);

        // Buscar el equipo por ID
        $equipo = $torneo->equipos()->findOrFail($equipoId);

        // Jugadores inscritos en este equipo en este torneo
        $jugadores = Jugador::whereHas('participaciones', function ($q) use ($id, $equipoId) {
            $q->where('torneo_id', $id)
                ->where('equipo_id', $equipoId);
        })->get();
        // Todos los jugadores del equipo (en general)
        $todosJugadoresDelEquipo = $equipo->jugadores;

        // Disponibles: los del equipo que NO están ya en este torneo
        $jugadoresDisponibles = $todosJugadoresDelEquipo->diff($jugadores);

        // Retornar la vista con los datos del torneo y del equipo
        return view('admin.jugadores_equipo_torneo', compact('torneo', 'equipo', 'jugadores', 'jugadoresDisponibles'));
    }

    public function agregarJugadorAEquipoEnTorneo(Request $request, $id, $equipoId)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'jugador_id' => 'required|exists:jugadores,id', // Asegurarse de que el jugador exista
            ],
            [
                'jugador_id.required' => 'El jugador es obligatorio.',
                'jugador_id.exists' => 'El jugador seleccionado no existe.',
            ]
        );

        // Buscar el torneo por ID
        $torneo = Torneo::findOrFail($id);
        // Buscar el equipo por ID
        $equipo = $torneo->equipos()->findOrFail($equipoId);
        // Buscar el jugador por ID
        $jugador = Jugador::findOrFail($validated['jugador_id']);

        // Inscribir el jugador al equipo en el torneo
        $equipo->jugadoresEnTorneos()->attach($jugador->id, ['torneo_id' => $torneo->id]);
        return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")->with('success', 'Jugador agregado al equipo en el torneo correctamente.');
    }

    public function eliminarJugadorDeEquipoEnTorneo($id, $equipoId, $jugadorId)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $torneo = Torneo::findOrFail($id);
        $equipo = $torneo->equipos()->findOrFail($equipoId);
        
        // Verificar si el jugador está inscrito en el equipo en el torneo
        if (!$equipo->jugadoresEnTorneos()
            ->wherePivot('torneo_id', $torneo->id)
            ->wherePivot('jugador_id', $jugadorId)
            ->exists()
        ){
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
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
                return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
            }
        // Validar los datos del formulario
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

        // Buscar el torneo por ID
        $torneo = Torneo::findOrFail($id);
        // Buscar el equipo por ID
        $equipo = $torneo->equipos()->findOrFail($equipoId);

        //verificar que el equipo esta inscrito al torneo
        if (!$torneo->equipos()->where('equipo_id', $equipoId)->exists()) {
            return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")
                ->withErrors(['El equipo no está inscrito en este torneo.']);
        }
        // Crear un nuevo jugador
        $jugador = new Jugador();
        $jugador->nombre = $validated['nombre'];
        $jugador->apellido1 = $validated['apellido1'];
        $jugador->apellido2 = $validated['apellido2'];
        $jugador->fecha_nacimiento = $validated['fecha_nacimiento'];
        $jugador->posicion = $validated['posicion'] ?? null;
        if ($request->hasFile('foto')) {
            $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre'] . '_' . $validated['apellido1'] . '_' . $validated['apellido2']);
            $timestamp = time();
            $extension = $request->file('foto')->getClientOriginalExtension();
            $fotoFileName = "foto_{$nombreJugador}_{$timestamp}.{$extension}";
            // Guarda directo en /public/jugadores_fotos
            $request->file('foto')->move(public_path('jugadores_fotos'), $fotoFileName);
            $jugador->foto = 'jugadores_fotos/' . $fotoFileName;
        } else {
            $jugador->foto = null; // Si no se subió una foto, establecerlo como nulo
        }
        $jugador->save();
        //Inscribir jugador en equipo
        $equipo->jugadores()->attach($jugador->id);
        $equipo->jugadoresEnTorneos()->attach($jugador->id, ['torneo_id' => $torneo->id]);
        return redirect("/admin/torneos/{$id}/equipos/{$equipoId}/jugadores")
            ->with('success', 'Jugador creado e inscrito en el equipo del torneo correctamente.');
    }

    ///////////////////////////USER////////////////////
    public function mostrarPaginaTorneosUser()
    {
        // Aquí deberías obtener los torneos desde la base de datos
        $torneos = Torneo::where('estado', 1)->get();
        // Retornar la vista con los datos de los torneos
        return view('user.torneos', compact('torneos'));
    }
}
