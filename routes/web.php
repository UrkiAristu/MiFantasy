<?php

use App\Http\Controllers\Auth\LoginController;
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
//LOGOUT
Route::get('/logout', function () {
    session()->flush(); // Borra todos los datos de la sesión
    return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
})->middleware('verificar.sesion');

////////////MIDLEWARE VERIFICAR SESION/////////
Route::middleware('verificar.sesion')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/home', function () {
        return view('welcome');
    });
});
