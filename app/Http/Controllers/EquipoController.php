<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
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
}
