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

    public function test_admin_local_can_create_equipo_in_managed_tournament()
    {
        $adminLocal = User::factory()->create(['admin' => false]);
        $torneo = Torneo::factory()->create();
        $adminLocal->torneosGestionados()->attach($torneo);

        $response = $this->actingAs($adminLocal)->post(route('admin.local.equipos.store'), [
            'nombre' => 'Equipo de Prueba Local',
            'torneos' => [$torneo->id],
        ]);

        $response->assertRedirect(route('admin.local.equipos.index'));
        $this->assertDatabaseHas('equipos', ['nombre' => 'Equipo de Prueba Local']);
        $equipo = Equipo::where('nombre', 'Equipo de Prueba Local')->first();
        $this->assertTrue($equipo->torneos->contains($torneo));
    }

    public function test_admin_local_cannot_create_equipo_in_unmanaged_tournament()
    {
        $adminLocal = User::factory()->create(['admin' => false]);
        $torneoAjeno = Torneo::factory()->create(); // No gestionado por adminLocal

        $response = $this->actingAs($adminLocal)->post(route('admin.local.equipos.store'), [
            'nombre' => 'Equipo Ajeno',
            'torneos' => [$torneoAjeno->id],
        ]);

        $response->assertSessionHasErrors('torneos');
        $this->assertDatabaseMissing('equipos', ['nombre' => 'Equipo Ajeno']);
    }

    public function test_admin_local_can_update_managed_equipo()
    {
        $adminLocal = User::factory()->create(['admin' => false]);
        $torneo = Torneo::factory()->create();
        $adminLocal->torneosGestionados()->attach($torneo);

        $equipo = Equipo::factory()->create(['nombre' => 'Nombre Viejo']);
        $equipo->torneos()->attach($torneo);

        $response = $this->actingAs($adminLocal)->put(route('admin.local.equipos.update', $equipo), [
            'nombre' => 'Nombre Actualizado',
            'torneos' => [$torneo->id],
        ]);

        $response->assertRedirect(route('admin.local.equipos.index'));
        $this->assertDatabaseHas('equipos', ['nombre' => 'Nombre Actualizado']);
    }

    public function test_admin_local_cannot_update_unmanaged_equipo()
    {
        $adminLocal = User::factory()->create(['admin' => false]);
        $torneoAjeno = Torneo::factory()->create();

        $equipoAjeno = Equipo::factory()->create(['nombre' => 'Equipo Ajeno']);
        $equipoAjeno->torneos()->attach($torneoAjeno);

        $response = $this->actingAs($adminLocal)->put(route('admin.local.equipos.update', $equipoAjeno), [
            'nombre' => 'Intento Hackeo',
            'torneos' => [$torneoAjeno->id],
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_local_can_destroy_managed_equipo()
    {
        $adminLocal = User::factory()->create(['admin' => false]);
        $torneo = Torneo::factory()->create();
        $adminLocal->torneosGestionados()->attach($torneo);

        $equipo = Equipo::factory()->create(['nombre' => 'Para Borrar']);
        $equipo->torneos()->attach($torneo);

        $response = $this->actingAs($adminLocal)->delete(route('admin.local.equipos.destroy', $equipo));

        $response->assertRedirect(route('admin.local.equipos.index'));
        $this->assertDatabaseMissing('equipos', ['id' => $equipo->id]);
    }
}
