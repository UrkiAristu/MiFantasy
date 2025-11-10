<?php

use App\Http\Middleware\RedirigirSiAutenticado;
use App\Http\Middleware\RedirigirSiVerificado;
use App\Http\Middleware\VerificarAdmin;
use App\Http\Middleware\VerificarSesion;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate as AuthMiddleware;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as GuestMiddleware;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as VerifiedMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth'     => AuthMiddleware::class,
            'guest'    => GuestMiddleware::class,
            'verified' => VerifiedMiddleware::class,
            'verificar.sesion' => VerificarSesion::class,
            'verificar.admin' => VerificarAdmin::class,
            'redirigir.si.autenticado' => RedirigirSiAutenticado::class,
            'redirigir.si.verificado' => RedirigirSiVerificado::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
