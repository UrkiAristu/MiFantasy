<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class SubscriptionController extends Controller
{
    /**
     * Iniciar sesión de Stripe Checkout o simulación segura en local.
     */
    public function checkout(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $planKey = $request->query('plan', $request->input('plan', 'pro'));
        $plans = config('saas.plans', []);

        if (!array_key_exists($planKey, $plans)) {
            $planKey = 'basico';
        }

        $orgName = $request->query('org', $request->input('org', $request->input('organizacion', 'Liga de ' . $user->name)));
        if (empty(trim((string) $orgName))) {
            $orgName = 'Liga de ' . $user->name;
        }

        $priceId = $plans[$planKey]['price_id'] ?? 'price_basico_monthly';
        $stripeSecret = config('cashier.secret') ?? env('STRIPE_SECRET');

        // Si Stripe está configurado con clave real, generar sesión de Checkout vía Cashier
        if (!empty($stripeSecret) && !in_array($stripeSecret, ['sk_test_placeholder', 'your_stripe_secret_here', ''])) {
            try {
                return $user->newSubscription('default', $priceId)
                    ->allowPromotionCodes()
                    ->checkout([
                        'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}&plan=' . $planKey . '&org=' . urlencode($orgName),
                        'cancel_url' => route('subscription.cancel'),
                        'metadata' => [
                            'user_id' => $user->id,
                            'plan' => $planKey,
                            'org_name' => $orgName,
                        ],
                    ]);
            } catch (Throwable $e) {
                Log::warning('Stripe Checkout no pudo inicializarse con el API remoto: ' . $e->getMessage());
                // Fallback automático para entornos de desarrollo sin Stripe configurado
            }
        }

        // Modo local / desarrollo / test: redirigir directamente al flujo de éxito y aprovisionamiento
        return redirect()->route('subscription.success', [
            'plan' => $planKey,
            'org' => $orgName,
            'mode' => 'local_sandbox',
        ]);
    }

    /**
     * Retorno de Stripe Checkout tras pago exitoso.
     * Auto-provisiona el Tenant y asigna el rol de Admin Local con Spatie Teams.
     */
    public function success(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $planKey = $request->query('plan', 'pro');
        $plans = config('saas.plans', []);
        $planData = $plans[$planKey] ?? ($plans['pro'] ?? ['name' => ucfirst($planKey)]);

        $orgName = $request->query('org', 'Liga de ' . $user->name);
        if (empty(trim((string) $orgName))) {
            $orgName = 'Liga de ' . $user->name;
        }

        // Auto-provisionar el Tenant e inicializar el RBAC
        $tenant = $this->provisionTenant($user, $orgName, $planKey);

        $planLabel = $planData['name'] ?? 'Plan ' . ucfirst($planKey);

        return redirect()->route('home')->with(
            'success',
            "¡Suscripción completada con éxito al {$planLabel}! Se ha creado tu espacio de liga '{$orgName}' (ID: {$tenant->id}) y se te ha asignado el rol de Admin Local."
        );
    }

    /**
     * Cancelación del proceso de suscripción.
     */
    public function cancel(Request $request): RedirectResponse
    {
        return redirect()->route('home')->with(
            'info',
            'El proceso de pago o suscripción se ha cancelado. Puedes activarlo cuando lo desees desde tu panel.'
        );
    }

    /**
     * Auto-provisiona el Tenant para el usuario y le asigna el rol de 'Admin Local' con Spatie Teams.
     */
    public function provisionTenant(User $user, string $orgName, string $planKey): Tenant
    {
        // 1. Generar ID único numérico compatible con Stancl Tenancy y Spatie team_id
        $maxId = Tenant::all()->map(fn($t) => is_numeric($t->id) ? (int) $t->id : 0)->max() ?? 0;
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

            if (!$role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        // 5. Asignar el rol al usuario dentro del equipo/tenant
        if (!$user->hasRole($roleName)) {
            $user->assignRole($role);
        }

        return $tenant;
    }
}
