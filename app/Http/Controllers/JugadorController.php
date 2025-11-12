<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JugadorController extends Controller
{
    ///////////////////////////ADMIN////////////////////
    public function mostrarPaginaJugadores()
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $jugadores = Jugador::all();

        // Retornar la vista con los datos de los jugadores
        return view('admin.jugadores', compact('jugadores'));
    }

    public function mostrarPaginaJugador($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Buscar el jugador por ID
        $jugador = Jugador::with(['equipos','participaciones'])->findOrFail($id);
        // Obtener equipos disponibles para agregar
        $equiposDisponibles = Equipo::whereNotIn('id', $jugador->equipos->pluck('id'))->get();
        // Retornar la vista con los datos del jugador
        return view('admin.jugador', compact('jugador', 'equiposDisponibles'));
    }

    public function crearJugador(Request $request)
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

        // Crear el jugador
        $jugador = new Jugador();
        $jugador->nombre = $validated['nombre'];
        $jugador->apellido1 = $validated['apellido1'];
        $jugador->apellido2 = $validated['apellido2'];
        $jugador->fecha_nacimiento = $validated['fecha_nacimiento'];
        $jugador->posicion = $validated['posicion'];

        // Manejar la subida de la foto
        if ($request->hasFile('foto')) {
            $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre'] . '_' . $validated['apellido1'] . '_' . $validated['apellido2']);
            $timestamp = time();
            $extension = $request->file('foto')->getClientOriginalExtension();
            $fotoFileName = "foto_{$nombreJugador}_{$timestamp}.{$extension}";
            // Guarda directo en /public/fotos_jugadores
            $request->file('foto')->move(public_path('fotos_jugadores'), $fotoFileName);

            // Guarda solo la ruta relativa para mostrarla
            $jugador->foto = 'fotos_jugadores/' . $fotoFileName;
        }
        $jugador->save();

        return redirect('/admin/jugadores')->with('success', 'Jugador creado exitosamente.');
    }
    public function eliminarJugador($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin){
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $jugador = Jugador::findOrFail($id);

        // Eliminar la foto del jugador si existe
        if ($jugador->foto && file_exists(public_path($jugador->foto))) {
            unlink(public_path($jugador->foto));
        }
        // Eliminar el jugador de la base de datos
        $jugador->delete();
        return redirect('/admin/jugadores')->with('success', 'Jugador eliminado correctamente.');
    }

    public function editarJugador(Request $request, $id)
    {
        // Verificar si el usuario es administrador
       if (!Auth::check() || !Auth::user()->admin){
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'nombre' => 'required|string|max:255',
                'apellido1' => 'required|string|max:255',
                'apellido2' => 'required|string|max:255',
                'fecha_nacimiento' => 'required|date',
                'posicion' => 'nullable|string|max:255', // Asumiendo que hay un campo para la posición
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Asumiendo que hay un campo para subir una foto
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

        // Buscar el jugador por ID
        $jugador = Jugador::findOrFail($id);
        $jugador->nombre = $validated['nombre'];
        $jugador->apellido1 = $validated['apellido1'];
        $jugador->apellido2 = $validated['apellido2'];
        $jugador->fecha_nacimiento = $validated['fecha_nacimiento'];
        $jugador->posicion = $validated['posicion'];
        if ($request->has('eliminar_foto') && $request->eliminar_foto) {
            // Eliminar la foto del jugador si se ha marcado la opción
            if ($jugador->foto && file_exists(public_path($jugador->foto))) {
                unlink(public_path($jugador->foto));
            }
            $jugador->foto = null; // Establecer la foto como nula
        }
        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($jugador->foto && file_exists(public_path($jugador->foto))) {
                unlink(public_path($jugador->foto));
            }
            $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre . '_' . $request->apellido1 . '_' . $request->apellido2);
            $timestamp = time();
            $extension = $request->file('foto')->getClientOriginalExtension();
            $fotoFileName = "foto_{$nombreJugador}_{$timestamp}.{$extension}";
            // Guarda directo en /public/jugadores_fotos
            $request->file('foto')->move(public_path('jugadores_fotos'), $fotoFileName);
            $jugador->foto = "jugadores_fotos/{$fotoFileName}";
        }

        $jugador->save();
        return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador actualizado correctamente.');
    }

    public function agregarAEquipoJugador(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin){
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

        $jugador = Jugador::findOrFail($id);
        // Comprobar que el jugador no esté ya en el equipo
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
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin){
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $jugador = Jugador::findOrFail($id);
        // Verificar si el jugador está en el equipo
        if (!$jugador->equipos()->where('equipo_id', $equipoId)->exists()) {
            return redirect("/admin/jugadores/{$id}")->withErrors(['El equipo no está inscrito en este torneo.']);
        }

        // Eliminar el jugador del equipo
        $jugador->equipos()->detach($equipoId);
        return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador eliminado del equipo correctamente.');

    }

    public function crearEquipoConJugador(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin){
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

        $jugador = Jugador::findOrFail($id);
        // Crear el equipo
        $equipo = new Equipo();
        $equipo->nombre = $validated['nombre'];
        if ($request->hasFile('logo')) {
            $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $validated['nombre']);
            $timestamp = time();
            $extension = $request->file('logo')->getClientOriginalExtension();
            $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";
            // Guarda directo en /public/equipos_logos
            $request->file('logo')->move(public_path('equipos_logos'), $logoFileName);
            $equipo->logo = "equipos_logos/{$logoFileName}";
        } else {
            $equipo->logo = null; // Si no se subió un logo, establecerlo como nulo
        }
        $equipo->save();

        // Asignar el equipo al jugador
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
