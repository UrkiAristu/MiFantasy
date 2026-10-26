<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BizumWebhookController extends Controller
{
    public function __construct(
        protected TenantProvisioningService $provisioningService
    ) {}

    /**
     * Maneja el webhook de conciliación para pagos por Bizum o transferencia manual.
     */
    public function handleWebhook(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true) ?? $request->all();
        $eventId = $payload['event_id'] ?? $payload['id'] ?? null;
        $eventType = $payload['type'] ?? 'bizum.payment.completed';
        $eventCreated = $payload['created'] ?? time();

        Log::info('Bizum Webhook Recibido', [
            'event_id' => $eventId,
            'event_type' => $eventType,
        ]);

        if ($eventId) {
            $processed = DB::table('webhook_events_processed')->where('id', $eventId)->exists();

            if ($processed) {
                Log::info("Bizum Webhook Idempotente: El evento {$eventId} ya fue procesado.");

                return new Response('Webhook Already Processed', 200);
            }

            DB::table('webhook_events_processed')->insert([
                'id' => $eventId,
                'type' => $eventType,
                'created_at_stripe' => Carbon::createFromTimestamp($eventCreated)->toDateTimeString(),
                'processed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $userId = $payload['user_id'] ?? $payload['metadata']['user_id'] ?? null;
        $orgName = $payload['org_name'] ?? $payload['metadata']['org_name'] ?? 'Mi Liga Bizum';
        $planKey = $payload['plan'] ?? $payload['metadata']['plan'] ?? 'pro';

        if (! $userId) {
            Log::warning('Bizum Webhook: Falta user_id en el payload.', $payload);

            return new Response('Missing User ID', 400);
        }

        /** @var User|null $user */
        $user = User::find($userId);

        if (! $user) {
            Log::warning("Bizum Webhook: Usuario ID {$userId} no encontrado.");

            return new Response('User Not Found', 404);
        }

        $existingTenant = Tenant::where('user_id', $user->id)
            ->where('name', $orgName)
            ->first();

        if ($existingTenant) {
            Log::info("Bizum Webhook Idempotente: Tenant '{$orgName}' ya existe para usuario ID {$user->id}.");

            return new Response('Tenant Already Exists', 200);
        }

        $this->provisioningService->provision($user, $orgName, $planKey);

        Log::info("Bizum Webhook: Tenant '{$orgName}' provisionado correctamente para usuario ID {$user->id}.");

        return new Response('OK', 200);
    }
}
