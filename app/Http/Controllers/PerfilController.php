<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function mostrarPaginaPerfil()
    {
        $user = Auth::user();
        return view('user/perfil',compact('user'));
    }

    
    public function actualizarPerfil(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:users,name,'.$user->id,
            'email' => 'required|email|max:100|unique:users,email,'.$user->id,
        ], [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'name.unique' => 'Ese nombre de usuario ya existe.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Introduce un email válido.',
            'email.unique' => 'Ese email ya está en uso.',
        ]);

        if ($validated['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        $user->save();


        return redirect('/user/perfil#actualizar-perfil')->with('success', 'Perfil actualizado correctamente.');
    }
    public function enviarVerificacionEmail()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Tu email ya está verificado.');
        }

        // Enviar correo
        $user->sendEmailVerificationNotification();

        return redirect('/user/perfil#actualizar-perfil')->with('status', 'verification-link-sent');
    }
    public function actualizarPassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.required' => 'Debes introducir tu contraseña actual.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Validar la contraseña actual
        if (! Hash::check($request->current_password, $user->password)) {
            return redirect('/user/perfil#actualizar-password')->withErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ]);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect('/user/perfil#actualizar-password')->with('password_success', 'Tu contraseña ha sido actualizada.');
    }

    public function eliminarPerfil(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'password_deletion' => ['required'],
        ], [
            'password_deletion.required' => 'Debes introducir tu contraseña actual.',
        ]);

        // Validar la contraseña
        if (! Hash::check($request->password_deletion, $user->password)) {
            return redirect('/user/perfil#eliminar-perfil')
            ->withErrors(['password_deletion' => 'La contraseña introducida no es correcta.'], 'userDeletion')
            ->with('delete_error', 'La contraseña introducida no es correcta.');
        }

        // Cerrar sesión
        Auth::logout();
        // Eliminar perfil
        $user->delete();

        return redirect('/')->with('success', 'Tu perfil ha sido eliminado.');
    }
}