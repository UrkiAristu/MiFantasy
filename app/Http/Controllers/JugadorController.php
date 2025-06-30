<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
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
                    'apellido2' => 'nullable|string|max:255',
                    'fecha_nacimiento' => 'required|date',
                    'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Asumiendo que hay un campo para subir una foto
                ],
                [
                    'nombre.required' => 'El nombre del jugador es obligatorio.',
                    'nombre.string' => 'El nombre del jugador debe ser una cadena de texto.',
                    'nombre.max' => 'El nombre del jugador no puede tener más de 255 caracteres.',
                    'apellido1.required' => 'El primer apellido del jugador es obligatorio.',
                    'apellido1.string' => 'El primer apellido del jugador debe ser una cadena de texto.',
                    'apellido1.max' => 'El primer apellido del jugador no puede tener más de 255 caracteres.',
                    'apellido2.string' => 'El segundo apellido del jugador debe ser una cadena de texto.',
                    'apellido2.max' => 'El segundo apellido del jugador no puede tener más de 255 caracteres.',
                    'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
                    'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
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
}
