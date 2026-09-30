<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * Manejar evento checkout.session.completed de Stripe.
     * Aprovisiona de forma asíncrona e idempotente el Tenant y los roles RBAC.
     */
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        $session = $payload['data']['object'] ?? [];
        $metadata = $session['metadata'] ?? [];

        $userId = $metadata['user_id'] ?? null;
        $planKey = $metadata['plan'] ?? 'pro';
        $orgName = $metadata['org_name'] ?? 'Mi Liga';

        // Buscar el usuario por user_id en metadata o por el customer ID de Stripe
        /** @var User|null $user */
        $user = $userId
            ? User::find($userId)
            : User::where('stripe_id', $session['customer'] ?? null)->first();

        if (! $user) {
            Log::warning('Stripe Webhook: Usuario no encontrado para checkout.session.completed', [
                'user_id' => $userId,
                'customer' => $session['customer'] ?? null,
            ]);

            return $this->successMethod();
        }

        // Verificación de Idempotencia: no crear el Tenant si ya fue aprovisionado
        $existingTenant = Tenant::where('user_id', $user->id)
            ->where('name', $orgName)
            ->first();

        if ($existingTenant) {
            Log::info("Webhook idempotente: Tenant '{$orgName}' ya existe para usuario ID {$user->id}. Omitiendo aprovisionamiento duplicado.");

            return $this->successMethod();
        }

        // Aprovisionar Tenant y configurar RBAC con Spatie Teams
        $this->provisionTenant($user, $orgName, $planKey);

        return $this->successMethod();
    }

    /**
     * Aprovisiona el Tenant para el usuario y le asigna el rol de 'Admin Local' con Spatie Teams.
     */
    public function provisionTenant(User $user, string $orgName, string $planKey): Tenant
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
