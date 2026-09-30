<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SubscriptionOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        // Limpiar caché de permisos de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_registration_with_saas_plan_redirects_to_checkout(): void
    {
        $response = $this->post('/registro', [
            'nombreUsuario' => 'OrganizadorTest',
            'email' => 'organizador@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'plan' => 'pro',
            'organizacion' => 'Liga Regional de Futbol',
        ]);

        $response->assertSessionHasNoErrors();
        $user = User::where('email', 'organizador@test.com')->first();
        $this->assertNotNull($user);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('subscription.checkout', [
            'plan' => 'pro',
            'org' => 'Liga Regional de Futbol',
        ]));
    }

    public function test_checkout_in_local_environment_redirects_to_success_provisioning(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.checkout', [
            'plan' => 'pro',
            'org' => 'Liga Municipal Master',
        ]));

        $response->assertRedirect(route('subscription.success', [
            'plan' => 'pro',
            'org' => 'Liga Municipal Master',
            'mode' => 'local_sandbox',
        ]));
    }

    public function test_success_url_is_read_only_and_does_not_provision_tenant_synchronously(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.success', [
            'plan' => 'pro',
            'org' => 'Liga Metropolitana',
        ]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        // Seguridad SEC-01: La ruta GET síncrona NO debe crear Tenants ni mutar base de datos
        $tenant = Tenant::where('user_id', $user->id)->first();
        $this->assertNull($tenant, 'La ruta GET de retorno no debe aprovisionar el Tenant de forma síncrona.');
    }

    public function test_stripe_webhook_checkout_session_completed_provisions_tenant_and_spatie_rbac(): void
    {
        $user = User::factory()->create();

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_abc123',
                    'customer' => 'cus_test_123',
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan' => 'pro',
                        'org_name' => 'Liga Metropolitana Webhook',
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/stripe/webhook', $payload);
        $response->assertStatus(200);

        // 1. Comprobar que el Tenant fue creado y vinculado al usuario vía Webhook asíncrono
        $tenant = Tenant::where('user_id', $user->id)->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Liga Metropolitana Webhook', $tenant->name);
        $this->assertEquals('pro', $tenant->plan);

        // 2. Comprobar RBAC multi-tenant con Spatie Teams
        setPermissionsTeamId($tenant->id);

        $this->assertTrue(
            $user->hasRole('Admin Local'),
            'El usuario debe tener asignado el rol Admin Local para el team_id del Tenant.'
        );

        $role = Role::where('name', 'Admin Local')
            ->where('team_id', $tenant->id)
            ->first();

        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('gestionar_torneos'));
        $this->assertTrue($role->hasPermissionTo('gestionar_equipos'));
        $this->assertTrue($role->hasPermissionTo('gestionar_jugadores'));
        $this->assertTrue($role->hasPermissionTo('ver_metricas_liga'));
    }

    public function test_stripe_webhook_is_idempotent_and_does_not_duplicate_tenant(): void
    {
        $user = User::factory()->create();

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_abc123',
                    'customer' => 'cus_test_123',
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan' => 'pro',
                        'org_name' => 'Liga Idempotente',
                    ],
                ],
            ],
        ];

        // Primer envío del webhook
        $response1 = $this->postJson('/stripe/webhook', $payload);
        $response1->assertStatus(200);
        $this->assertEquals(1, Tenant::where('user_id', $user->id)->count());

        // Segundo envío duplicado por reintento de Stripe
        $response2 = $this->postJson('/stripe/webhook', $payload);
        $response2->assertStatus(200);

        // Debe mantenerse exactamente 1 Tenant (idempotencia)
        $this->assertEquals(1, Tenant::where('user_id', $user->id)->count());
    }

    public function test_cancel_redirects_to_home_with_info_notice(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.cancel'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('info');
    }

    public function test_webhook_deduplication_stores_event_id(): void
    {
        $payload = [
            'id' => 'evt_test_dedup_12345',
            'type' => 'invoice.payment_succeeded',
            'created' => time(),
            'data' => [
                'object' => [
                    'id' => 'in_test_123',
                ],
            ],
        ];

        $response1 = $this->postJson('/stripe/webhook', $payload);
        $response1->assertStatus(200);

        $this->assertDatabaseHas('webhook_events_processed', [
            'id' => 'evt_test_dedup_12345',
            'type' => 'invoice.payment_succeeded',
        ]);

        // Second call with same event ID
        $response2 = $this->postJson('/stripe/webhook', $payload);
        $response2->assertStatus(200);
        $response2->assertSeeText('Webhook Already Processed');
    }

    public function test_billing_portal_without_stripe_id_redirects_with_notice(): void
    {
        $user = User::factory()->create(['stripe_id' => null]);

        $response = $this->actingAs($user)->get(route('subscription.portal'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('info');
    }

    public function test_webhook_accepts_valid_cryptographic_signature(): void
    {
        $secret = 'whsec_test_signing_secret_key_12345';
        config(['cashier.webhook.secret' => $secret]);
        config(['cashier.webhook.tolerance' => 300]);

        $payload = [
            'id' => 'evt_valid_sig_' . uniqid(),
            'type' => 'customer.subscription.updated',
            'created' => time(),
            'data' => [
                'object' => [
                    'id' => 'sub_test_valid',
                    'customer' => 'cus_test_123',
                ],
            ],
        ];

        $payloadJson = json_encode($payload);
        $timestamp = time();
        $signatureHeader = \Stripe\WebhookSignature::generateSignatureHeader($payloadJson, $secret, $timestamp);

        $response = $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => $signatureHeader,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payloadJson
        );

        $response->assertStatus(200);
    }

    public function test_webhook_rejects_forged_cryptographic_signature(): void
    {
        $secret = 'whsec_test_signing_secret_key_12345';
        config(['cashier.webhook.secret' => $secret]);

        $payload = [
            'id' => 'evt_forged_sig_' . uniqid(),
            'type' => 'invoice.payment_succeeded',
            'created' => time(),
            'data' => [
                'object' => [
                    'id' => 'in_test_forged',
                ],
            ],
        ];

        $payloadJson = json_encode($payload);
        // Generar firma con secret incorrecto / forjada
        $forgedSignatureHeader = \Stripe\WebhookSignature::generateSignatureHeader($payloadJson, 'whsec_attacker_evil_secret', time());

        $response = $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => $forgedSignatureHeader,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payloadJson
        );

        // AccessDeniedHttpException produce código 403
        $this->assertContains($response->getStatusCode(), [400, 403]);
    }

    public function test_webhook_rejects_replayed_timestamp_outside_tolerance(): void
    {
        $secret = 'whsec_test_signing_secret_key_12345';
        config(['cashier.webhook.secret' => $secret]);
        config(['cashier.webhook.tolerance' => 300]);

        $payload = [
            'id' => 'evt_replayed_sig_' . uniqid(),
            'type' => 'invoice.payment_succeeded',
            'created' => time() - 600,
            'data' => [
                'object' => [
                    'id' => 'in_test_replay',
                ],
            ],
        ];

        $payloadJson = json_encode($payload);
        // Generar firma con timestamp de hace 600 segundos (supera los 300s de tolerancia)
        $replayedTimestamp = time() - 600;
        $replayedHeader = \Stripe\WebhookSignature::generateSignatureHeader($payloadJson, $secret, $replayedTimestamp);

        $response = $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => $replayedHeader,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payloadJson
        );

        // Replay fuera de tolerancia produce 403 / 400
        $this->assertContains($response->getStatusCode(), [400, 403]);
    }
}
