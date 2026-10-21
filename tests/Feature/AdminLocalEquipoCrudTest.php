<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLocalEquipoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_local_can_view_equipos_index()
    {
        $adminLocal = User::factory()->create(['admin' => false]);
        $torneo = Torneo::factory()->create();
        $adminLocal->torneosGestionados()->attach($torneo);

        $equipo = Equipo::factory()->create();
        $equipo->torneos()->attach($torneo);

        $response = $this->actingAs($adminLocal)->get(route('admin.local.equipos.index'));

        $response->assertStatus(200);
        $response->assertSee($equipo->nombre);
    }
}
