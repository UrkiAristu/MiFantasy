<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TorneoController extends Controller
{
    public function mostrarPaginaTorneos()
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Aquí deberías obtener los torneos desde la base de datos
            $torneos = Torneo::all(); // Reemplaza esto con la lógica para obtener los torneos

            // Retornar la vista con los datos de los torneos
            return view('admin.torneos', compact('torneos')); // Asegúrate de tener una vista llamada 'admin.torneos'
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function mostrarPaginaTorneo($id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Buscar el torneo por ID
            $torneo = Torneo::find($id);
            $equiposDisponibles = [];
            if ($torneo) {
                // Obtener equipos disponibles (no inscritos en el torneo)
                $equiposDisponibles = Equipo::whereNotIn('id', $torneo->equipos->pluck('id'))->get();
                // Retornar la vista con los datos del torneo
                return view('admin.torneo', compact('torneo', 'equiposDisponibles')); // Asegúrate de tener una vista llamada 'admin.torneo'
            } else {
                return redirect('/admin/torneos')->withErrors(['Torneo no encontrado.']);
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }
    public function crearTorneo(Request $request)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required|string|max:255',
                    'descripcion' => 'nullable|string|max:1000', // Asumiendo que hay un campo de descripción
                    'fecha_inicio' => 'required|date',
                    'fecha_fin' => 'required|date|after_or_equal:fecha_inicio', // Asegurarse de que la fecha de fin sea igual o posterior a la fecha de inicio
                    'estado' => 'boolean', // Asumiendo que hay un campo para activar/desactivar el torneo
                    'logo' => 'nullable|image|max:2048', // Asumiendo que hay un campo para subir un logo
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
                ]
            );

            if ($validator->fails()) {
                return redirect('/admin/torneos')
                    ->withErrors($validator)
                    ->withInput();
            }
            // Crear un nuevo torneo
            $torneo = new Torneo();
            $torneo->nombre = $request->nombre;
            $torneo->descripcion = $request->descripcion ?? null; // Asumiendo que hay un campo de descripción
            $torneo->fecha_inicio = $request->fecha_inicio;
            $torneo->fecha_fin = $request->fecha_fin;
            $torneo->estado = $request->estado ?? false; // Asumiendo que hay un campo para activar/desactivar el torneo
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

            return redirect('/admin/torneos')->with('success', 'Torneo creado correctamente.');
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function eliminarTorneo($id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $torneo = Torneo::find($id);
            if ($torneo) {
                // Eliminar el logo del torneo si existe
                if ($torneo->logo && file_exists(public_path($torneo->logo))) {
                    unlink(public_path($torneo->logo));
                }
                // Eliminar el torneo de la base de datos
                $torneo->delete();
                return redirect('/admin/torneos')->with('success', 'Torneo eliminado correctamente.');
            } else {
                return redirect('/admin/torneos')->withErrors(['Torneo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function editarTorneo(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Validar los datos del formulario
            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required|string|max:255',
                    'descripcion' => 'nullable|string|max:1000', // Asumiendo que hay un campo de descripción
                    'fecha_inicio' => 'required|date',
                    'fecha_fin' => 'required|date|after_or_equal:fecha_inicio', // Asegurarse de que la fecha de fin sea igual o posterior a la fecha de inicio
                    'estado' => 'boolean', // Asumiendo que hay un campo para activar/desactivar el torneo
                    'logo' => 'nullable|image|max:2048', // Asumiendo que hay un campo para subir un logo
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
                ]
            );

            if ($validator->fails()) {
                return redirect("/admin/torneos/{$id}")
                    ->withErrors($validator)
                    ->withInput();
            }

            // Buscar el torneo por ID
            $torneo = Torneo::find($id);
            if ($torneo) {
                $torneo->nombre = $request->nombre;
                $torneo->descripcion = $request->descripcion ?? null; // Asumiendo que hay un campo de descripción
                $torneo->fecha_inicio = $request->fecha_inicio;
                $torneo->fecha_fin = $request->fecha_fin;
                $torneo->estado = $request->estado ?? false; // Asumiendo que hay un campo para activar/desactivar el torneo
                if ($request->has('eliminar_logo') && $request->eliminar_logo) {
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
                    $nombreTorneo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre);
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
        } else {
            return redirect('/admin/torneos')->withErrors(['Torneo no encontrado.']);
        }
    }

    public function agregarEquipoATorneo(Request $request, $id)
    {

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
            return redirect("/admin/torneos/{$id}")
                ->withErrors($validator)
                ->withInput();
        }

        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Buscar el torneo por ID
            $torneo = Torneo::find($id);
            if ($torneo) {
                // Buscar el equipo por ID
                $equipo = Equipo::find($request->equipo_id);
                if ($equipo) {
                    // Agregar el equipo al torneo
                    $torneo->equipos()->attach($equipo->id);
                    return redirect("/admin/torneos/{$id}")->with('success', 'Equipo agregado al torneo correctamente.');
                } else {
                    return redirect("/admin/torneos/{$id}")->withErrors(['Equipo no encontrado.']);
                }
            } else {
                return redirect('/admin/torneos')->withErrors(['Torneo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function eliminarEquipoDeTorneo($id, $equipoId)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            // Buscar el torneo por ID
            $torneo = Torneo::find($id);
            if ($torneo) {
                // Verificar si el equipo está inscrito en el torneo
                if ($torneo->equipos()->where('equipo_id', $equipoId)->exists()) {
                    // Eliminar el equipo del torneo
                    $torneo->equipos()->detach($equipoId);
                    return redirect("/admin/torneos/{$id}")->with('success', 'Equipo eliminado del torneo correctamente.');
                } else {
                    return redirect("/admin/torneos/{$id}")->withErrors(['El equipo no está inscrito en este torneo.']);
                }
            } else {
                return redirect('/admin/torneos')->withErrors(['Torneo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function crearEquipoEnTorneo(Request $request, $id)
    {
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
            return redirect("/admin/torneos/{$id}")
                ->withErrors($validator)
                ->withInput();
        }
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $torneo = Torneo::find($id);
            if ($torneo) {
                // Crear un nuevo equipo
                $equipo = new Equipo();
                $equipo->nombre = $request->nombre;
                if ($request->hasFile('logo')) {
                    $nombreEquipo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nombre);
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
            } else {
                return redirect('/admin/torneos')->withErrors(['Torneo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }
}
