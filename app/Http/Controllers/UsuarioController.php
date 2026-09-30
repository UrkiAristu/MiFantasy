<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function mostrarPaginaUsuarios()
    {
        $usuarios = User::all();
        return view('admin.usuarios', ['usuarios' => $usuarios]);
    }

    public function mostrarPaginaUsuario($id)
    {
        $usuario = User::findOrFail($id);
        return view('admin.usuario', ['usuario' => $usuario]);
    }

    public function editarUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

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

        $usuario->name   = $validated['name'];
        $usuario->email  = $validated['email'];
        $usuario->admin  = (bool)$validated['admin'];
        $usuario->active = (bool)$validated['active'];
        $usuario->save();

        return redirect('/admin/usuarios/' . $id)->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleActivo($id)
    {
        $usuario = User::findOrFail($id);

        // Evita que un admin se autodesactive
        if (Auth::id() === (int)$id) {
            return redirect('/admin/usuarios')
                ->withErrors(['No puedes inhabilitar tu propia cuenta.']);
        }
        // Evita dejar el sistema sin admins
        if ($usuario->admin && $usuario->active && User::where('admin', true)->where('active', true)->count() === 1) {
            return back()->withErrors(['No puedes inhabilitar al último admin activo.']);
        }

        $usuario->active = !$usuario->active;
        $usuario->save();
        $mensaje = $usuario->active ? 'habilitado' : 'inhabilitado';
        return redirect('/admin/usuarios')->with('success', "Usuario {$usuario->name} {$mensaje} correctamente.");
    }
}

