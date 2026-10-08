<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\TenantDashboardController;
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
});
