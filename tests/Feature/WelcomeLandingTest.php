<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_visitor_can_view_welcome_landing_page()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MiFantasy');
        // Hemos eliminado las aserciones de textos antiguos que ya no están en la nueva landing
    }

    public function test_authenticated_user_visiting_root_sees_home_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        // Ahora nuestro sistema redirige ordenadamente a los usuarios autenticados
        $response->assertStatus(302);
        // Opcional: puedes ser más estricto y verificar a dónde redirige
        $response->assertRedirect('/home');
    }
}
