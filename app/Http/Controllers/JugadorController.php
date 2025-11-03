<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JugadorController extends Controller
{
    public function mostrarPaginaJugadores()
    {
        // Aquí deberías obtener los jugadores desde la base de datos
        $jugadores = Jugador::all(); // Reemplaza esto con la lógica para obtener los jugadores

        // Retornar la vista con los datos de los jugadores
        return view('admin.jugadores', compact('jugadores')); // Asegúrate de tener una vista llamada 'admin.jugadores'
    }

    public function mostrarPaginaJugador($id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Buscar el jugador por ID
            $jugador = Jugador::find($id);
            $equiposDisponibles = [];
            if ($jugador) {
                $equiposDisponibles = Equipo::whereNotIn('id', $jugador->equipos->pluck('id'))->get();
                // Retornar la vista con los datos del jugador
                return view('admin.jugador', compact('jugador', 'equiposDisponibles')); // Asegúrate de tener una vista llamada 'admin.jugador'
            } else {
                return redirect('/admin/jugadores')->withErrors(['Jugador no encontrado.']);
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function crearJugador(Request $request)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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
            if ($validator->fails()) {
                return redirect('/admin/jugadores')
                    ->withErrors($validator)
                    ->withInput();
            }

            // Crear el jugador
            $jugador = new Jugador();
            $jugador->nombre = $request->nombre;
            $jugador->apellido1 = $request->apellido1;
            $jugador->apellido2 = $request->apellido2;
            $jugador->fecha_nacimiento = $request->fecha_nacimiento;
            $jugador->posicion = $request->posicion;

            // Manejar la subida de la foto
            if ($request->hasFile('foto')) {
                $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre . '_' . $request->apellido1 . '_' . $request->apellido2);
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
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }
    public function eliminarJugador($id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $jugador = Jugador::find($id);
            if ($jugador) {
                // Eliminar la foto del jugador si existe
                if ($jugador->foto && file_exists(public_path($jugador->foto))) {
                    unlink(public_path($jugador->foto));
                }
                // Eliminar el jugador de la base de datos
                $jugador->delete();
                return redirect('/admin/jugadores')->with('success', 'Jugador eliminado correctamente.');
            } else {
                return redirect('/admin/jugadores')->withErrors(['Jugador no encontrado.']);
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function editarJugador(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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

            if ($validator->fails()) {
                return redirect("/admin/jugadores/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            // Buscar el jugador por ID
            $jugador = Jugador::find($id);
            if ($jugador) {
                $jugador->nombre = $request->nombre;
                $jugador->apellido1 = $request->apellido1;
                $jugador->apellido2 = $request->apellido2;
                $jugador->fecha_nacimiento = $request->fecha_nacimiento;
                $jugador->posicion = $request->posicion;
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
            }

            // Guardar los cambios en la base de datos
            $jugador->save();
            return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador actualizado correctamente.');
        } else {
            return redirect('/admin/jugadores')->withErrors(['Jugador no encontrado.']);
        }
    }

    public function agregarAEquipoJugador(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
                [
                    'equipo_id' => 'required|exists:equipos,id', // Asegurarse de que el equipo exista
                ],
                [
                    'equipo_id.required' => 'El equipo es obligatorio.',
                    'equipo_id.exists' => 'El equipo seleccionado no existe.',
                ]
            );

            if ($validator->fails()) {
                return redirect("/admin/jugadores/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            $jugador = Jugador::find($id);
            if ($jugador) {
                $equipoId = $request->equipo_id;
                $equipo = Equipo::find($equipoId);
                if ($equipo) {
                    // Agregar el jugador al equipo
                    $equipo->jugadores()->attach($jugador->id);
                    return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador agregado al equipo correctamente.');
                } else {
                    return redirect("/admin/jugadores/{$id}")->withErrors(['Equipo no encontrado.']);
                }
            } else {
                return redirect('/admin/jugadores')->withErrors(['Jugador no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function eliminarDeEquipoJugador($id, $equipoId)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $jugador = Jugador::find($id);
            if ($jugador) {
                $equipo = Equipo::find($equipoId);
                if ($equipo) {
                    // Eliminar el jugador del equipo
                    $equipo->jugadores()->detach($jugador->id);
                    return redirect("/admin/jugadores/{$id}")->with('success', 'Jugador eliminado del equipo correctamente.');
                } else {
                    return redirect("/admin/jugadores/{$id}")->withErrors(['Equipo no encontrado.']);
                }
            } else {
                return redirect('/admin/jugadores')->withErrors(['Jugador no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function crearEquipoConJugador(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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

            if ($validator->fails()) {
                return redirect("/admin/jugadores/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            $jugador = Jugador::find($id);
            if ($jugador) {
                // Crear el equipo
                $equipo = new Equipo();
                $equipo->nombre = $request->nombre;
                if ($request->hasFile('logo')) {
                    $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre . '_' . $request->apellido1 . '_' . $request->apellido2);
                    $timestamp = time();
                    $extension = $request->file('logo')->getClientOriginalExtension();
                    $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";
                    // Guarda directo en /public/jugadores_fotos
                    $request->file('logo')->move(public_path('jugadores_fotos'), $logoFileName);
                    $equipo->logo = "jugadores_fotos/{$logoFileName}";
                } else {
                    $equipo->logo = null; // Si no se subió un logo, establecerlo como nulo
                }
                $equipo->save();

                // Asignar el equipo al jugador
                $jugador->equipos()->attach($equipo->id);
                return redirect("/admin/jugadores/{$id}")->with('success', 'Equipo creado e inscrito el jugador correctamente.');
            } else {
                return redirect("/admin/jugadores/{$id}")->withErrors(['Jugador no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

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
