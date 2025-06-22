<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    dd(session()->all());
});



////////////MIDLEWARE REDIRIGIR SI AUTENTICADO/////////
Route::middleware('redirigir.si.autenticado')->group(function () {
    //REGISTER
    //Mostrar página de registro
    Route::get('/registro', function () {
        return view('user/registro');
    });
    Route::post('/registro', [LoginController::class, 'registro']);

    //LOGIN
    //Mostrar página de login
    Route::get('/login', function () {
        return view('user/login');
    });
    Route::post('/login', [LoginController::class, 'login']);
});

////////////MIDLEWARE VERIFICAR SESION/////////
Route::middleware('verificar.sesion')->group(function () {
    Route::get('/', function () {
        return view('user/home');
    });
    Route::get('/home', function () {
        return view('user/home');
    });
    //LOGOUT
    Route::get('/logout', function () {
        session()->flush(); // Borra todos los datos de la sesión
        return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
    })->middleware('verificar.sesion');
    ///////////////MIDLEWARE VERIFICAR ADMIN/////////
    Route::middleware('verificar.admin')->group(function () {
        Route::get('/zonaAdmin', function () {
            return view('admin/home');
        });
        Route::get('/admin/usuarios', [UsuarioController::class, 'mostrarPaginaUsuarios']);
        Route::get('/admin/usuarios/{id}', [UsuarioController::class, 'mostrarPaginaUsuario']);
        Route::post('admin/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario']);
    });
});
