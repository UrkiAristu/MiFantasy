<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    dd(session('centro'));
});

//LOGIN
//Mostrar página de login
Route::get('/login', function () {
    return view('user/login');
});
Route::post('/login', [LoginController::class, 'login']);

//REGISTER
//Mostrar página de registro
Route::get('/registro', function () {
    return view('user/registro');
});
Route::post('/registro', [LoginController::class, 'registro']);
