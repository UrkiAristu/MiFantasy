<?php

namespace App\Http\Controllers;

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
            return view('admin.torneos', ['torneos' => $torneos]);
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
                $torneo->delete();
                return redirect('/admin/torneos')->with('success', 'Torneo eliminado correctamente.');
            } else {
                return redirect('/admin/torneos')->withErrors(['Torneo no encontrado.']);
            }
        } else {
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }
}
