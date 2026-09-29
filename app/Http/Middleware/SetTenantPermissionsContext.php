<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantPermissionsContext
{
    /**
     * Handle an incoming request and set Spatie Permission team context for multi-tenancy.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (function_exists('tenant') && tenant('id')) {
            setPermissionsTeamId(tenant('id'));
        } elseif ($request->session()->has('current_tenant_id')) {
            setPermissionsTeamId($request->session()->get('current_tenant_id'));
        } elseif ($request->user() && isset($request->user()->tenant_id)) {
            setPermissionsTeamId($request->user()->tenant_id);
        }

        return $next($request);
    }
}
