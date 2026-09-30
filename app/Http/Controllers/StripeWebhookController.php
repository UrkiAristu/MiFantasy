<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * @pagokit:signature-verified (Regla 1: Cashier via VerifyWebhookSignature verifica el raw body con Stripe\Webhook)
     * Regla 2: Cashier comprueba el timestamp de la firma permitiendo una deriva máxima de 300s (config('cashier.webhook.tolerance')).
     * Regla 8: Usa cashier.webhook.secret que debe ser distinto de la API Key.
     * Regla 9: Cashier usa la SDK oficial para comprobar la firma (hash_equals seguro contra timing attacks).
     */
    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $eventId = $payload['id'] ?? null;
        $eventType = $payload['type'] ?? 'unknown';
        $eventCreated = $payload['created'] ?? null;

        // Regla 7: Loguear solo id, type, created. NUNCA el payload completo.
        Log::info('Stripe Webhook Recibido', [
            'event_id'      => $eventId,
            'event_type'    => $eventType,
            'event_created' => $eventCreated,
        ]);

        if ($eventId) {
            // Deduplicación de Webhooks: registrar el evento para evitar procesar dos veces el mismo (replay / fallos de red)
            $processed = DB::table('webhook_events_processed')->where('id', $eventId)->exists();

            if ($processed) {
                Log::info("Webhook idempotente: El evento {$eventId} ya fue procesado.");
                return new Response('Webhook Already Processed', 200);
            }

            DB::table('webhook_events_processed')->insert([
                'id'                => $eventId,
                'type'              => $eventType,
                'created_at_stripe' => $eventCreated ? Carbon::createFromTimestamp($eventCreated)->toDateTimeString() : null,
                'processed_at'      => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        return parent::handleWebhook($request);
    }

    /**
     * Handle handled events directly if needed or let Cashier's parent handle them.
     * customer.subscription.created, customer.subscription.updated, customer.subscription.deleted are handled by parent.
     * invoice.payment_succeeded and invoice.payment_failed are handled by parent.
     */

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
