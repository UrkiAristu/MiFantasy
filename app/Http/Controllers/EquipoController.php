<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipoController extends Controller
{
    public function mostrarPaginaEquipos()
    {
        // Aquí deberías obtener los equipos desde la base de datos
        $equipos = Equipo::all(); // Reemplaza esto con la lógica para obtener los equipos

        // Retornar la vista con los datos de los equipos
        return view('admin.equipos', compact('equipos')); // Asegúrate de tener una vista llamada 'admin.equipos'
    }
    public function mostrarPaginaEquipo($id)
    {
        // Aquí deberías obtener el equipo por su ID desde la base de datos
        $equipo = Equipo::find($id); // Reemplaza esto con la lógica para obtener el equipo
        $torneosDisponibles = [];
        $jugadoresDisponibles = [];
        if ($equipo) {
            // Obtener torneos disponibles (no inscritos el equipo)
            $torneosDisponibles = Torneo::whereNotIn('id', $equipo->torneos->pluck('id'))->get();
            // Obtener jugadores disponibles (no asignados al equipo)
            $jugadoresDisponibles = Jugador::whereNotIn('id', $equipo->jugadores->pluck('id'))->get();
            // Retornar la vista con los datos del equipo
            return view('admin.equipo', compact('equipo', 'torneosDisponibles', 'jugadoresDisponibles')); // Asegúrate de tener una vista llamada 'admin.equipo'
        } else {
            return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
        }
    }

    public function crearEquipo(Request $request)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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
            if ($validator->fails()) {
                return redirect('/admin/equipos')
                    ->withErrors($validator)
                    ->withInput();
            }
            // Crear el equipo
            $equipo = new Equipo();
            $equipo->nombre = $request->nombre;
            if ($request->hasFile('logo')) {
                $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre);
                $timestamp = time();
                $extension = $request->file('logo')->getClientOriginalExtension();
                $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";
                // Guarda directo en /public/equipos_logos
                $request->file('logo')->move(public_path('equipos_logos'), $logoFileName);

                // Guarda solo la ruta relativa para mostrarla
                $equipo->logo = 'equipos_logos/' . $logoFileName;
            } else {
                $equipo->logo = null; // Si no se subió un logo, establecerlo como nulo
            }
            $equipo->save();

            return redirect('/admin/equipos')->with('success', 'Equipo creado correctamente.');
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function eliminarEquipo($id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $equipo = Equipo::find($id);
            if ($equipo) {
                // Eliminar el logo del equipo si existe
                if ($equipo->logo && file_exists(public_path($equipo->logo))) {
                    unlink(public_path($equipo->logo));
                }
                // Eliminar el equipo de la base de datos
                $equipo->delete();
                return redirect('/admin/equipos')->with('success', 'Equipo eliminado correctamente.');
            } else {
                return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function editarEquipo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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

            if ($validator->fails()) {
                return redirect("/admin/equipos/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            // Buscar el equipo por ID
            $equipo = Equipo::find($id);
            if ($equipo) {
                $equipo->nombre = $request->nombre;
                if ($request->has('eliminar_logo') && $request->eliminar_logo) {
                    // Eliminar el logo del equipo si se ha marcado la opción
                    if ($equipo->logo && file_exists(public_path($equipo->logo))) {
                        unlink(public_path($equipo->logo));
                    }
                    $equipo->logo = null; // Establecer el logo como nulo
                }
                if ($request->hasFile('logo')) {
                    // Eliminar el logo anterior si existe
                    if ($equipo->logo && file_exists(public_path($equipo->logo))) {
                        unlink(public_path($equipo->logo));
                    }
                    $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre);
                    $timestamp = time();
                    $extension = $request->file('logo')->getClientOriginalExtension();
                    $logoFileName = "logo_{$nombreEquipo}_{$timestamp}.{$extension}";
                    // Guarda directo en /public/equipos_logos
                    $request->file('logo')->move(public_path('equipos_logos'), $logoFileName);
                    $equipo->logo = "equipos_logos/{$logoFileName}";
                }
            }

            // Guardar los cambios en la base de datos
            $equipo->save();

            return redirect("/admin/equipos/{$id}")->with('success', 'Equipo actualizado correctamente.');
        } else {
            return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
        }
    }

    public function inscribirATorneoEquipo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
                [
                    'torneo_id' => 'required|exists:torneos,id', // Asegurarse de que el torneo exista
                ],
                [
                    'torneo_id.required' => 'El torneo es obligatorio.',
                    'torneo_id.exists' => 'El torneo seleccionado no existe.',
                ]
            );

            if ($validator->fails()) {
                return redirect("/admin/equipos /{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            $equipo = Equipo::find($id);
            if ($equipo) {
                $torneoId = $request->torneo_id;
                $torneo = Torneo::find($torneoId);
                if ($torneo) {
                    // Inscribir el equipo al torneo
                    $equipo->torneos()->attach($torneoId);
                    return redirect("/admin/equipos/{$id}")->with('success', 'Equipo inscrito al torneo correctamente.');
                } else {
                    return redirect("/admin/equipos/{$id}")->withErrors(['Torneo no encontrado.']);
                }
            } else {
                return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function eliminarDeTorneoEquipo($id, $torneoId)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $equipo = Equipo::find($id);
            if ($equipo) {
                $torneo = Torneo::find($torneoId);
                if ($torneo) {
                    // Desinscribir el equipo del torneo
                    $equipo->torneos()->detach($torneoId);
                    return redirect("/admin/equipos/{$id}")->with('success', 'Equipo eliminado del torneo correctamente.');
                } else {
                    return redirect("/admin/equipos/{$id}")->withErrors(['Torneo no encontrado.']);
                }
            } else {
                return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function crearTorneoConEquipo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $request->merge([
                'usa_posiciones' => $request->has('usa_posiciones') ? 1 : 0,
            ]);
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
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

            if ($validator->fails()) {
                return redirect("/admin/equipos/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            $equipo = Equipo::find($id);
            if ($equipo) {
                // Crear el torneo
                $torneo = new Torneo();
                $torneo->nombre = $request->nombre;
                $torneo->descripcion = $request->descripcion;
                $torneo->fecha_inicio = $request->fecha_inicio;
                $torneo->fecha_fin = $request->fecha_fin;
                $torneo->estado = $request->estado;
                $torneo->jugadores_por_equipo = $request->jugadores_por_equipo;
                $torneo->usa_posiciones = $request->usa_posiciones;
                if ($request->hasFile('logo')) {
                    $nombreTorneo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre);
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

                // Inscribir el equipo al torneo
                $equipo->torneos()->attach($torneo->id);

                return redirect("/admin/equipos/{$id}")->with('success', 'Torneo creado e inscrito al equipo correctamente.');
            } else {
                return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function agregarJugadorAEquipo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
                [
                    'jugador_id' => 'required|exists:jugadores,id', // Asegurarse de que el jugador exista
                ],
                [
                    'jugador_id.required' => 'El jugador es obligatorio.',
                    'jugador_id.exists' => 'El jugador seleccionado no existe.',
                ]
            );

            if ($validator->fails()) {
                return redirect("/admin/equipos/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            $equipo = Equipo::find($id);
            if ($equipo) {
                $jugadorId = $request->jugador_id;
                $jugador = Jugador::find($jugadorId);
                if ($jugador) {
                    // Agregar el jugador al equipo
                    $equipo->jugadores()->attach($jugadorId);
                    return redirect("/admin/equipos/{$id}")->with('success', 'Jugador agregado al equipo correctamente.');
                } else {
                    return redirect("/admin/equipos/{$id}")->withErrors(['Jugador no encontrado.']);
                }
            } else {
                return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function eliminarJugadorDeEquipo($id, $jugadorId)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $equipo = Equipo::find($id);
            if ($equipo) {
                $jugador = Jugador::find($jugadorId);
                if ($jugador) {
                    // Eliminar el jugador del equipo
                    $equipo->jugadores()->detach($jugadorId);
                    return redirect("/admin/equipos/{$id}")->with('success', 'Jugador eliminado del equipo correctamente.');
                } else {
                    return redirect("/admin/equipos/{$id}")->withErrors(['Jugador no encontrado.']);
                }
            } else {
                return redirect('/admin/equipos')->withErrors(['Equipo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function crearJugadorEnEquipo(Request $request, $id)
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

            if ($validator->fails()) {
                return redirect("/admin/equipos/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            $equipo = Equipo::find($id);
            if ($equipo) {
                // Crear el jugador
                $jugador = new Jugador();
                $jugador->nombre = $request->nombre;
                $jugador->apellido1 = $request->apellido1;
                $jugador->apellido2 = $request->apellido2;
                $jugador->fecha_nacimiento = $request->fecha_nacimiento;
                $jugador->posicion = $request->posicion;
                if ($request->hasFile('foto')) {
                    $nombreJugador = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre . '_' . $request->apellido1 . '_' . $request->apellido2);
                    $timestamp = time();
                    $extension = $request->file('foto')->getClientOriginalExtension();
                    $fotoFileName = "foto_{$nombreJugador}_{$timestamp}.{$extension}";
                    // Guarda directo en /public/jugadores_fotos
                    $request->file('foto')->move(public_path('jugadores_fotos'), $fotoFileName);
                    $jugador->foto = "jugadores_fotos/{$fotoFileName}";
                } else {
                    $jugador->foto = null; // Si no se subió una foto, establecerlo como nulo
                }
                $jugador->save();

                // Asignar el jugador al equipo
                $equipo->jugadores()->attach($jugador->id);
                return redirect("/admin/equipos/{$id}")->with('success', 'Jugador creado e inscrito en el equipo correctamente.');
            } else {
                return redirect("/admin/equipos/{$id}")->withErrors(['Equipo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }
}
