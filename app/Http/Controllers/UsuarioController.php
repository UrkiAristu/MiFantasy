<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function mostrarPaginaUsuarios()
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $cuentas = Cuenta::all();
            // Retornar la vista con los datos de las cuentas
            return view('admin.usuarios', ['usuarios' => $cuentas]);
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }
    public function mostrarPaginaUsuario($id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $cuenta = Cuenta::find($id);
            if ($cuenta) {
                // Retornar la vista con los datos de la cuenta
                return view('admin.usuario', ['usuario' => $cuenta]);
            } else {
                return redirect('/admin/usuarios')->withErrors(['Usuario no encontrado.']);
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }

    public function editarUsuario(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (session('admin')) {
            $cuenta = Cuenta::find($id);
            if ($cuenta) {
                // Validar los datos del formulario
                $request->validate([
                    'nombreUsuario' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:cuentas,email,' . $id,
                    'admin' => 'required|boolean',
                    'activo' => 'required|boolean',
                ]);

                // Actualizar los datos de la cuenta
                $cuenta->nombreUsuario = $request->nombreUsuario;
                $cuenta->email = $request->email;
                $cuenta->admin = $request->admin;
                $cuenta->activo = $request->activo;
                $cuenta->save();

                return redirect('/admin/usuarios/' . $id)->with('success', 'Usuario actualizado correctamente.');
            } else {
                return redirect('/admin/usuarios')->withErrors(['Usuario no encontrado.']);
            }
        } else {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
    }
}
