<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(
            [
                'login' => 'required|string',
                'password' => 'required|string|min:8',
                'remember' => 'sometimes|boolean',
            ],
            [
                'login.required' => 'El email  o nombre de usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.'
            ]);

        $login = $request->login;

        // Detecta si es email o username (name)
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $field     => $login,
            'password' => $request->password,
            'active'   => 1, // solo usuarios activos pueden autenticarse
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/'); // o /dashboard
        }
        throw ValidationException::withMessages([
            'login' => 'El usuario/contraseña no son correctos o la cuenta está inactiva.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function register(Request $request)
    {
       $validated = $request->validate(
            [
                'nombreUsuario' => 'required|unique:cuentas',
                'email' => 'required|unique:cuentas',
                'password' => 'required|confirmed|min:8',
            ],
            [
                'nombreUsuario.required' => 'El nombre de usuario es obligatorio.',
                'nombreUsuario.unique' => 'El nombre de usuario ya existe.',
                'email.required' => 'El email es obligatorio.',
                'email.unique' => 'El email ya existe.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.'
            ]
        );

        // Crear el usuario en la tabla users
        $user = User::create([
            'name'     => $validated['nombreUsuario'],   // <- mapeo a users.name
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'active'   => true,   // o false si quieres activación manual
            'admin'    => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'Registro exitoso. ¡Bienvenido, '.$user->name.'!');
    }
}
