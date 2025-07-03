<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\TorneoController;
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
        //USUARIOS
        Route::get('/admin/usuarios', [UsuarioController::class, 'mostrarPaginaUsuarios']);
        Route::get('/admin/usuarios/{id}', [UsuarioController::class, 'mostrarPaginaUsuario']);
        Route::post('admin/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario']);
        Route::get('/admin/usuarios/{id}/inhabilitar', [UsuarioController::class, 'inhabilitarUsuario']);
        Route::get('/admin/usuarios/{id}/habilitar', [UsuarioController::class, 'habilitarUsuario']);
        //Torneos
        Route::get('/admin/torneos', [TorneoController::class, 'mostrarPaginaTorneos']);
        Route::get('/admin/torneos/{id}', [TorneoController::class, 'mostrarPaginaTorneo']);
        Route::post('/admin/torneos/crear', [TorneoController::class, 'crearTorneo']);
        Route::get('/admin/torneos/{id}/eliminar', [TorneoController::class, 'eliminarTorneo']);
        Route::post('/admin/torneos/{id}/editar', [TorneoController::class, 'editarTorneo']);
        Route::post('/admin/torneos/{id}/equipos/agregar', [TorneoController::class, 'agregarEquipoATorneo']);
        Route::get('/admin/torneos/{id}/equipos/{equipoId}/eliminar', [TorneoController::class, 'eliminarEquipoDeTorneo']);
        Route::post('/admin/torneos/{id}/equipos/crear', [TorneoController::class, 'crearEquipoEnTorneo']);
        Route::get('/admin/torneos/{id}/equipos/{equipoId}/jugadores', [TorneoController::class, 'mostrarJugadoresDeEquipoEnTorneo']);
        //Equipos
        Route::get('/admin/equipos', [EquipoController::class, 'mostrarPaginaEquipos']);
        Route::get('/admin/equipos/{id}', [EquipoController::class, 'mostrarPaginaEquipo']);
        Route::post('/admin/equipos/crear', [EquipoController::class, 'crearEquipo']);
        Route::get('/admin/equipos/{id}/eliminar', [EquipoController::class, 'eliminarEquipo']);
        Route::post('/admin/equipos/{id}/editar', [EquipoController::class, 'editarEquipo']);
        Route::post('/admin/equipos/{id}/torneos/agregar', [EquipoController::class, 'inscribirATorneoEquipo']);
        Route::get('/admin/equipos/{id}/torneos/{torneoId}/eliminar', [EquipoController::class, 'eliminarDeTorneoEquipo']);
        Route::post('/admin/equipos/{id}/torneos/crear', [EquipoController::class, 'crearTorneoConEquipo']);
        Route::post('/admin/equipos/{id}/jugadores/agregar', [EquipoController::class, 'agregarJugadorAEquipo']);
        Route::get('/admin/equipos/{id}/jugadores/{jugadorId}/eliminar', [EquipoController::class, 'eliminarJugadorDeEquipo']);
        Route::post('/admin/equipos/{id}/jugadores/crear', [EquipoController::class, 'crearJugadorEnEquipo']);
        //Jugadores
        Route::get('/admin/jugadores', [JugadorController::class, 'mostrarPaginaJugadores']);
        Route::get('/admin/jugadores/{id}', [JugadorController::class, 'mostrarPaginaJugador']);
        Route::post('/admin/jugadores/crear', [JugadorController::class, 'crearJugador']);
        Route::get('/admin/jugadores/{id}/eliminar', [JugadorController::class, 'eliminarJugador']);
        Route::post('/admin/jugadores/{id}/editar', [JugadorController::class, 'editarJugador']);
        Route::post('/admin/jugadores/{id}/equipos/agregar', [JugadorController::class, 'agregarAEquipoJugador']);
        Route::get('/admin/jugadores/{id}/equipos/{equipoId}/eliminar', [JugadorController::class, 'eliminarDeEquipoJugador']);
        Route::post('/admin/jugadores/{id}/equipos/crear', [JugadorController::class, 'crearEquipoConJugador']);
    });
});
