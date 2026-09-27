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

    public function test_success_provisions_tenant_and_assigns_admin_local_role_with_spatie_teams(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.success', [
            'plan' => 'pro',
            'org' => 'Liga Metropolitana',
        ]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        // 1. Comprobar que el Tenant fue creado y vinculado al usuario
        $tenant = Tenant::where('user_id', $user->id)->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Liga Metropolitana', $tenant->name);
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

    public function test_cancel_redirects_to_home_with_info_notice(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.cancel'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('info');
    }
}
