<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nombreUsuario' => 'required',
                'password' => 'required|min:0',
            ],
            [
                'nombreUsuario.required' => 'El email  o nombre de usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.'
            ]
        );

        if ($validator->fails()) {
            return redirect('/login')->withErrors($validator)->withInput();;
        }

        $nombreUsuario = $request->nombreUsuario;
        $password = $request->password;

        //Obtenemos la cuenta con el nombredeusuario o el email
        $cuenta = Cuenta::where('nombreUsuario', $nombreUsuario)->orWhere('email', $nombreUsuario)->first();
        if (!$cuenta) {
            //Devuelve los errores en json
            return redirect('/login')->withErrors(['El nombre de usuario o la contraseña son incorrectos.'])->withInput();
        }

        //Comprobamos la contraseña
        if (!password_verify($password, $cuenta->password)) {
            //Devuelve los errores en json
            return redirect('/login')->withErrors(['El nombre de usuario o la contraseña son incorrectos.'])->withInput();
        }

        //Si todo es correcto, iniciamos sesión
        session(['cuenta' => $cuenta->id, 'nombreUsuario' => $cuenta->nombreUsuario, 'email' => $cuenta->email]);
        return redirect('/')->with('success', 'Inicio de sesión exitoso.');
    }
    public function registro(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
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

        if ($validator->fails()) {
            return redirect('/registro')->withErrors($validator)->withInput();
        }

        $nombreUsuario = $request->nombreUsuario;
        $email = $request->email;
        $password = $request->password;

        $cuenta = new Cuenta();
        $cuenta->nombreUsuario = $nombreUsuario;
        $cuenta->email = $email;
        $cuenta->password = password_hash($password, PASSWORD_DEFAULT);
        $cuenta->save();
        //Iniciar sesión automáticamente después del registro
        session(['cuenta' => $cuenta->id, 'nombreUsuario' => $cuenta->nombreUsuario, 'email' => $cuenta->email]);
        return redirect('/')->with('success', 'Registro exitoso. Bienvenido, ' . $nombreUsuario . '!');
    }
}
