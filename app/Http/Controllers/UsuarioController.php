<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function mostrarPaginaUsuarios()
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $usuarios = User::all();
        // Retornar la vista con los datos de los usuarios
        return view('admin.usuarios', ['usuarios' => $usuarios]);
    }
    public function mostrarPaginaUsuario($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin){
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $usuario = User::findOrFail($id);

        // Retornar la vista con los datos de la cuenta
        return view('admin.usuario', ['usuario' => $usuario]);

    }

    public function editarUsuario(Request $request, $id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $usuario = User::findOrFail($id);
        
        // Validar los datos del formulario
        $validated = $request->validate(
            [
                'name'   => 'required|string|max:50|unique:users,name,'.$id,
                'email'  => 'required|email|max:100|unique:users,email,'.$id,
                'admin'  => 'required|boolean',
                'active' => 'required|boolean',
            ],
            [
                'name.required'   => 'El nombre de usuario es obligatorio.',
                'name.unique'     => 'Ese nombre de usuario ya existe.',
                'email.required'  => 'El email es obligatorio.',
                'email.email'     => 'Introduce un email válido.',
                'email.unique'    => 'Ese email ya está en uso.',
                'admin.required'  => 'Debes indicar si es administrador.',
                'admin.boolean'   => 'El valor de admin debe ser 0 o 1.',
                'active.required' => 'Debes indicar si la cuenta está activa.',
                'active.boolean'  => 'El valor de active debe ser 0 o 1.',
            ]
        );

        // Actualizar los datos de la cuenta
        $usuario->name   = $validated['name'];
        $usuario->email  = $validated['email'];
        $usuario->admin  = (bool)$validated['admin'];
        $usuario->active = (bool)$validated['active'];
        $usuario->save();

        return redirect('/admin/usuarios/' . $id)->with('success', 'Usuario actualizado correctamente.');
            
    }

    public function toggleActivo($id)
    {
        // Verificar si el usuario es administrador
        if (!Auth::check() || !Auth::user()->admin) {
            // Si no es administrador, redirigir a la página de inicio o mostrar un error
            return redirect('/')->withErrors(['No tienes permiso para acceder a esta página.']);
        }
        $usuario = User::find($id);

        // Evita que un admin se autodesactive
        if (Auth::id() === $id) {
            return redirect('/admin/usuarios')
                ->withErrors(['No puedes inhabilitar tu propia cuenta.']);
        }
        //Evita dejar el sistema sin admins
        if ($usuario->admin && $usuario->active && User::where('admin', true)->where('active', true)->count() === 1) {
            return back()->withErrors(['No puedes inhabilitar al último admin activo.']);
        }

        $usuario->active = !$usuario->active;
        $usuario->save();
        $mensaje = $usuario->active ? 'habilitado' : 'inhabilitado';
        return redirect('/admin/usuarios')->with('success', "Usuario {$usuario->name} {$mensaje} correctamente.");
    }
}
