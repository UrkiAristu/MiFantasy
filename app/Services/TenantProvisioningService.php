<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class TenantProvisioningService
{
    /**
     * Aprovisiona el Tenant para el usuario y le asigna el rol de 'Admin Local' con Spatie Teams.
     */
    public function provision(User $user, string $orgName, string $planKey): Tenant
    {
        // 1. Generar ID único numérico compatible con Stancl Tenancy y Spatie team_id evitando OOM
        $driver = DB::connection()->getDriverName();
        $maxId = $driver === 'sqlite'
            ? (int) (DB::table('tenants')->selectRaw('MAX(CAST(id AS INTEGER)) as max_id')->value('max_id') ?? 0)
            : (int) (DB::table('tenants')->selectRaw('MAX(CAST(id AS UNSIGNED)) as max_id')->value('max_id') ?? 0);
        $tenantId = (string) ($maxId + 1);

        // 2. Crear registro de Tenant
        /** @var Tenant $tenant */
        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => $orgName,
            'user_id' => $user->id,
            'plan' => $planKey,
            'data' => [
                'name' => $orgName,
                'user_id' => $user->id,
                'plan' => $planKey,
                'owner_email' => $user->email,
            ],
        ]);

        // 3. Configurar Spatie Permission Teams para este Tenant
        setPermissionsTeamId($tenant->id);

        $roleName = config('saas.default_admin_role', 'Admin Local');
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
            'team_id' => $tenant->id,
        ]);

        // 4. Crear y asignar permisos específicos del Admin Local
        $permissions = config('saas.default_permissions', []);
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);

            if (! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        // 5. Asignar el rol al usuario dentro del equipo/tenant
        if (! $user->hasRole($roleName)) {
            $user->assignRole($role);
        }

        return $tenant;
    }
}
