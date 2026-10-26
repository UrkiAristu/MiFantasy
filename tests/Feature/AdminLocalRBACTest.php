<?php

namespace Tests\Feature;

use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLocalRBACTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_normal_no_puede_acceder_al_dashboard_local()
    {
        $user = User::factory()->create(['admin' => false]);
        $response = $this->actingAs($user)->get(route('admin.local.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_local_puede_acceder_a_su_dashboard()
    {
        $user = User::factory()->create(['admin' => false]);
        $torneo = Torneo::factory()->create();
        $user->torneosGestionados()->attach($torneo->id);

        $response = $this->actingAs($user)->get(route('admin.local.dashboard'));
        $response->assertStatus(200);
    }

    public function test_admin_local_no_puede_crear_equipos_globales()
    {
        $user = User::factory()->create(['admin' => false]);
        $torneo = Torneo::factory()->create();
        $user->torneosGestionados()->attach($torneo->id);

        $response = $this->actingAs($user)->post('/admin/equipos/crear', [
            'nombre' => 'Equipo Prohibido',
        ]);
        $response->assertStatus(403);
    }
}
