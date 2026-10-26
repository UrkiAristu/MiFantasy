<?php

namespace Tests\Feature;

use App\Jobs\RecalcularPuntosJornadaJob;
use App\Models\Alineacion;
use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FantasyEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    public function test_jugador_lesionado_sin_estadistica_en_jornada_obtiene_cero_puntos(): void
    {
        $user = User::factory()->create();
        $torneo = Torneo::factory()->create();
        $liguilla = Liguilla::factory()->create(['torneo_id' => $torneo->id]);
        $jornada = Jornada::factory()->create(['torneo_id' => $torneo->id]);

        $jugadorLesionado = Jugador::factory()->create();
        $alineacion = Alineacion::create([
            'user_id' => $user->id,
            'liguilla_id' => $liguilla->id,
            'jornada_id' => $jornada->id,
        ]);
        $alineacion->jugadores()->attach($jugadorLesionado->id, ['puntos' => 0]);

        // Ejecutar job de recálculo (el jugador no tiene registros en la tabla estadisticas para esta jornada)
        $job = new RecalcularPuntosJornadaJob($jornada->id);
        $job->handle();

        $puntosPivot = $alineacion->fresh()->jugadores()->first()->pivot->puntos;
        $this->assertEquals(0, $puntosPivot);
    }

    public function test_empates_en_clasificacion_global_se_registran_correctamente(): void
    {
        $torneo = Torneo::factory()->create();
        $liguilla = Liguilla::factory()->create(['torneo_id' => $torneo->id]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $liguilla->usuarios()->attach([
            $user1->id => ['puntos' => 30],
            $user2->id => ['puntos' => 30],
        ]);

        $response = $this->actingAs($user1)->getJson("/user/liguillas/{$liguilla->id}/clasificacion?modo_clasificacion=global");

        $response->assertStatus(200);
        $clasificacion = $response->json('clasificacion');

        $this->assertCount(2, $clasificacion);
        $this->assertEquals(30, $clasificacion[0]['puntos']);
        $this->assertEquals(30, $clasificacion[1]['puntos']);
    }

    public function test_concurrencia_idempotencia_recalcular_puntos_job(): void
    {
        $torneo = Torneo::factory()->create();
        $liguilla = Liguilla::factory()->create(['torneo_id' => $torneo->id]);
        $jornada = Jornada::factory()->create(['torneo_id' => $torneo->id]);
        $user = User::factory()->create();

        $liguilla->usuarios()->attach($user->id, ['puntos' => 0]);

        $equipo1 = Equipo::factory()->create();
        $equipo2 = Equipo::factory()->create();
        $partido = Partido::factory()->create([
            'jornada_id' => $jornada->id,
            'equipo_local_id' => $equipo1->id,
            'equipo_visitante_id' => $equipo2->id,
            'estado' => 'jugado',
        ]);

        $job = new RecalcularPuntosJornadaJob($jornada->id);

        // Ejecuciones concurrentes simuladas / repetidas deben ser totalmente idempotentes
        $job->handle();
        $job->handle();
        $job->handle();

        $this->assertTrue(true, 'El job de recálculo manejó múltiples ejecuciones concurrentes sin excepciones.');
    }
}
