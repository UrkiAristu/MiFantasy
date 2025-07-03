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
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $equipo = Equipo::find($id);
            if ($equipo) {
                $torneoId = $request->input('torneo_id');
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

    public function agregarJugadorAEquipo(Request $request, $id)
    {
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

        // Verificar si el usuario es administrador
        if (session('admin')) {
            $equipo = Equipo::find($id);
            if ($equipo) {
                $jugadorId = $request->input('jugador_id');
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
}
