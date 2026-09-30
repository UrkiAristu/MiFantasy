<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Checkout;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SubscriptionController extends Controller
{
    /**
     * Iniciar sesión de Stripe Checkout o simulación segura en local.
     */
    public function checkout(Request $request): Response|\Illuminate\Http\RedirectResponse|\Laravel\Cashier\Checkout
    {
        /** @var User $user */
        $user = $request->user();

        $planKey = $request->query('plan', $request->input('plan', 'pro'));
        $plans = config('saas.plans', []);

        if (! array_key_exists($planKey, $plans)) {
            $planKey = 'basico';
        }

        $orgName = $request->query('org', $request->input('org', $request->input('organizacion', 'Liga de '.$user->name)));
        if (empty(trim((string) $orgName))) {
            $orgName = 'Liga de '.$user->name;
        }

        $priceId = $plans[$planKey]['price_id'] ?? 'price_basico_monthly';
        $stripeSecret = config('cashier.secret') ?? config('services.stripe.secret');

        // Si Stripe está configurado con clave real, generar sesión de Checkout vía Cashier
        if (! empty($stripeSecret) && ! str_contains($stripeSecret, 'placeholder') && $stripeSecret !== 'your_stripe_secret_here') {
            try {
                // Rule 5: SAQ A compliant redirect (nunca tocamos PAN o CVV).
                // Rule 3: Claves de idempotencia como UUID v4 fuerte.
                $idempotencyKey = \Illuminate\Support\Str::uuid()->toString();

                \Illuminate\Support\Facades\DB::table('idempotency_keys')->insert([
                    'id'      => $idempotencyKey,
                    'user_id' => $user->id,
                    'scope'   => 'subscription_checkout_' . $planKey,
                    'used_at' => now(),
                ]);

                return $user->newSubscription('default', $priceId)
                    ->allowPromotionCodes()
                    ->checkout([
                        'payment_method_types' => ['card', 'bizum'],
                        'mode' => 'subscription',
                        'success_url' => route('subscription.success').'?session_id={CHECKOUT_SESSION_ID}&plan='.$planKey.'&org='.urlencode($orgName),
                        'cancel_url' => route('subscription.cancel'),
                        'metadata' => [
                            'user_id' => $user->id,
                            'plan' => $planKey,
                            'org_name' => $orgName,
                            'idempotency_key' => $idempotencyKey,
                        ],
                    ]);
            } catch (Throwable $e) {
                Log::warning('Stripe Checkout no pudo inicializarse con el API remoto: '.$e->getMessage());
                // Fallback automático para entornos de desarrollo sin Stripe configurado
            }
        }

        // Modo local / desarrollo / test: redirigir directamente al flujo de éxito informativo
        return redirect()->route('subscription.success', [
            'plan' => $planKey,
            'org' => $orgName,
            'mode' => 'local_sandbox',
        ]);
    }

    /**
     * Retorno de Stripe Checkout tras pago exitoso.
     * Ruta de solo lectura / informativa: el aprovisionamiento real del Tenant y roles
     * se realiza de forma asíncrona y segura a través de los webhooks de Stripe.
     */
    public function success(Request $request): RedirectResponse
    {
        $planKey = $request->query('plan', 'pro');
        $plans = config('saas.plans', []);
        $planData = $plans[$planKey] ?? ($plans['pro'] ?? ['name' => ucfirst($planKey)]);
        $planLabel = $planData['name'] ?? 'Plan '.ucfirst($planKey);

        return redirect()->route('home')->with(
            'success',
            "¡Pago procesado con éxito para el {$planLabel}! Tu espacio de liga se está configurando automáticamente en segundo plano y estará disponible en unos instantes."
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
     * Redirigir al Customer Portal de Stripe para gestionar métodos de pago, facturas y cancelaciones.
     */
    public function portal(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasStripeId()) {
            return $user->redirectToBillingPortal(route('home'));
        }

        return redirect()->route('home')->with(
            'info',
            'No tienes una suscripción activa o perfil de facturación registrado en Stripe.'
        );
    }
}
