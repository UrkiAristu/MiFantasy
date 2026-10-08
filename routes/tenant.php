<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\TenantDashboardController;
use App\Http\Controllers\Tenant\TenantEquipoController;
use App\Http\Controllers\Tenant\TenantJugadorController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class
])->group(function () {
    Route::get('/test', fn() => 'Tenant test works');
    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');
    Route::resource('equipos', TenantEquipoController::class)->names('tenant.equipos');
    Route::resource('jugadores', TenantJugadorController::class)->names('tenant.jugadores');
});
