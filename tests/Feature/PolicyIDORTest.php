<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyIDORTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test horizontal tenant isolation on Torneos.
     */
    public function test_admin_local_cannot_view_or_update_torneo_belonging_to_another_tenant(): void
    {
        $adminLocalA = User::factory()->create(['admin' => false]);
        $adminLocalB = User::factory()->create(['admin' => false]);

        $torneoA = Torneo::factory()->create();
        $torneoB = Torneo::factory()->create();

        $adminLocalA->torneosGestionados()->attach($torneoA->id);
        $adminLocalB->torneosGestionados()->attach($torneoB->id);

        $this->actingAs($adminLocalA);

        $this->assertFalse($adminLocalA->can('view', $torneoB));
        $this->assertFalse($adminLocalA->can('update', $torneoB));
        $this->assertFalse($adminLocalA->can('delete', $torneoB));

        $this->assertTrue($adminLocalA->can('view', $torneoA));
        $this->assertTrue($adminLocalA->can('update', $torneoA));
    }

    /**
     * Test horizontal tenant isolation on Equipos.
     */
    public function test_admin_local_cannot_update_or_delete_equipo_of_another_tenant(): void
    {
        $adminLocalA = User::factory()->create(['admin' => false]);
        $adminLocalB = User::factory()->create(['admin' => false]);

        $torneoA = Torneo::factory()->create();
        $torneoB = Torneo::factory()->create();

        $adminLocalA->torneosGestionados()->attach($torneoA->id);
        $adminLocalB->torneosGestionados()->attach($torneoB->id);

        $equipoA = Equipo::factory()->create();
        $equipoB = Equipo::factory()->create();

        $torneoA->equipos()->attach($equipoA->id);
        $torneoB->equipos()->attach($equipoB->id);

        $this->actingAs($adminLocalA);

        $this->assertFalse($adminLocalA->can('update', $equipoB));
        $this->assertFalse($adminLocalA->can('delete', $equipoB));

        $this->assertTrue($adminLocalA->can('update', $equipoA));
        $this->assertTrue($adminLocalA->can('delete', $equipoA));
    }

    /**
     * Test horizontal tenant isolation on Jornadas.
     */
    public function test_admin_local_cannot_update_or_delete_jornada_of_another_tenant(): void
    {
        $adminLocalA = User::factory()->create(['admin' => false]);
        $adminLocalB = User::factory()->create(['admin' => false]);

        $torneoA = Torneo::factory()->create();
        $torneoB = Torneo::factory()->create();

        $adminLocalA->torneosGestionados()->attach($torneoA->id);
        $adminLocalB->torneosGestionados()->attach($torneoB->id);

        $jornadaA = Jornada::factory()->create(['torneo_id' => $torneoA->id]);
        $jornadaB = Jornada::factory()->create(['torneo_id' => $torneoB->id]);

        $this->actingAs($adminLocalA);

        $this->assertFalse($adminLocalA->can('update', $jornadaB));
        $this->assertFalse($adminLocalA->can('delete', $jornadaB));

        $this->assertTrue($adminLocalA->can('update', $jornadaA));
        $this->assertTrue($adminLocalA->can('delete', $jornadaA));
    }

    /**
     * Test horizontal tenant isolation on Partidos.
     */
    public function test_admin_local_cannot_update_or_delete_partido_of_another_tenant(): void
    {
        $adminLocalA = User::factory()->create(['admin' => false]);
        $adminLocalB = User::factory()->create(['admin' => false]);

        $torneoA = Torneo::factory()->create();
        $torneoB = Torneo::factory()->create();

        $adminLocalA->torneosGestionados()->attach($torneoA->id);
        $adminLocalB->torneosGestionados()->attach($torneoB->id);

        $jornadaA = Jornada::factory()->create(['torneo_id' => $torneoA->id]);
        $jornadaB = Jornada::factory()->create(['torneo_id' => $torneoB->id]);

        $partidoA = Partido::factory()->create(['jornada_id' => $jornadaA->id]);
        $partidoB = Partido::factory()->create(['jornada_id' => $jornadaB->id]);

        $this->actingAs($adminLocalA);

        $this->assertFalse($adminLocalA->can('update', $partidoB));
        $this->assertFalse($adminLocalA->can('delete', $partidoB));

        $this->assertTrue($adminLocalA->can('update', $partidoA));
        $this->assertTrue($adminLocalA->can('delete', $partidoA));
    }
}
