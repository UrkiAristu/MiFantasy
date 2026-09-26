<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_visitor_can_view_welcome_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MiFantasy');
        $response->assertSee('Software SaaS para Organizadores de Ligas Amateur');
        $response->assertSee('Entorno Privado y Aislado');
        $response->assertSee('Panel de Gestión Propio');
        $response->assertSee('Motor Táctico 11 / 7 / Sala');
        $response->assertSee('Plan Básico');
        $response->assertSee('Plan Pro');
        $response->assertSee('Plan Enterprise');
        $response->assertSee(route('login'));
        $response->assertSee(route('register'));
    }

    public function test_authenticated_user_visiting_root_sees_home_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('user.home');
    }
}
