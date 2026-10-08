<?php

namespace Tests\Feature;

use App\Http\Controllers\PartidoController;
use App\Jobs\CongelarJornadaJob;
use App\Jobs\RecalcularPuntosJornadaJob;
use App\Models\Alineacion;
use App\Models\Equipo;
use App\Models\Estadistica;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Partido;
use App\Models\Plantilla;
use App\Models\Torneo;
use App\Models\User;
use App\Services\CongelarAlineacionesService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PuntosYClasificacionTest extends TestCase
{
    use RefreshDatabase;

    private Torneo $torneo;

    private Liguilla $liguilla;

    private Jornada $jornada1;

    private Jornada $jornada2;

    private User $userA;

    private User $userB;

    private array $jugadores;

    private Equipo $equipo1;

    private Equipo $equipo2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        Cache::store(config('cache.default'))->flush();

        // 1. Crear usuarios
        $this->userA = User::factory()->create(['name' => 'Usuario A', 'email' => 'a@test.com', 'active' => true]);
        $this->userB = User::factory()->create(['name' => 'Usuario B', 'email' => 'b@test.com', 'active' => true]);

        // 2. Crear Torneo
        $this->torneo = new Torneo;
        $this->torneo->nombre = 'Torneo Clasificacion Test';
        $this->torneo->jugadores_por_equipo = 2;
        $this->torneo->fecha_inicio = now()->subDays(10);
        $this->torneo->fecha_fin = now()->addDays(20);
        $this->torneo->save();

        // 3. Crear Liguilla y asociar usuarios
        $this->liguilla = new Liguilla;
        $this->liguilla->nombre = 'Liguilla Clasificacion';
        $this->liguilla->torneo_id = $this->torneo->id;
        $this->liguilla->max_usuarios = 10;
        $this->liguilla->codigo_unico = 'LIGCLAS1';
        $this->liguilla->creador_id = $this->userA->id;
        $this->liguilla->save();

        $this->liguilla->usuarios()->attach([
            $this->userA->id => ['puntos' => 0],
            $this->userB->id => ['puntos' => 0],
        ]);

        // 4. Crear Equipos
        $this->equipo1 = new Equipo;
        $this->equipo1->nombre = 'Equipo 1';
        $this->equipo1->save();

        $this->equipo2 = new Equipo;
        $this->equipo2->nombre = 'Equipo 2';
        $this->equipo2->save();

        // 5. Crear Jugadores
        $this->jugadores = [];
        for ($i = 1; $i <= 4; $i++) {
            $jugador = new Jugador;
            $jugador->nombre = "Jugador $i";
            $jugador->apellido1 = "Apellido $i";
            $jugador->save();
            $this->jugadores[] = $jugador;

            // Inscribir en equipos para el torneo
            $equipoId = ($i <= 2) ? $this->equipo1->id : $this->equipo2->id;
            DB::table('equipo_jugador_torneo')->insert([
                'jugador_id' => $jugador->id,
                'equipo_id' => $equipoId,
                'torneo_id' => $this->torneo->id,
                'goles' => 0,
                'asistencias' => 0,
                'puntos' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Asignar plantillas
        // User A tiene Jugador 1 y Jugador 2
        $plantillaA = Plantilla::create(['liguilla_id' => $this->liguilla->id, 'user_id' => $this->userA->id]);
        $plantillaA->jugadores()->attach([$this->jugadores[0]->id, $this->jugadores[1]->id]);

        // User B tiene Jugador 3 y Jugador 4
        $plantillaB = Plantilla::create(['liguilla_id' => $this->liguilla->id, 'user_id' => $this->userB->id]);
        $plantillaB->jugadores()->attach([$this->jugadores[2]->id, $this->jugadores[3]->id]);

        // 7. Crear Alineaciones Base
        $alineacionBaseA = Alineacion::create(['user_id' => $this->userA->id, 'liguilla_id' => $this->liguilla->id, 'jornada_id' => null]);
        $alineacionBaseA->jugadores()->attach([$this->jugadores[0]->id, $this->jugadores[1]->id]);

        $alineacionBaseB = Alineacion::create(['user_id' => $this->userB->id, 'liguilla_id' => $this->liguilla->id, 'jornada_id' => null]);
        $alineacionBaseB->jugadores()->attach([$this->jugadores[2]->id, $this->jugadores[3]->id]);

        // 8. Crear Jornadas
        $this->jornada1 = new Jornada;
        $this->jornada1->torneo_id = $this->torneo->id;
        $this->jornada1->nombre = 'Jornada 1';
        $this->jornada1->orden = 1;
        $this->jornada1->fecha_inicio = now()->subDays(4);
        $this->jornada1->fecha_fin = now()->subDays(2);
        $this->jornada1->save();

        $this->jornada2 = new Jornada;
        $this->jornada2->torneo_id = $this->torneo->id;
        $this->jornada2->nombre = 'Jornada 2';
        $this->jornada2->orden = 2;
        $this->jornada2->fecha_inicio = now()->subDay();
        $this->jornada2->fecha_fin = now()->addDays(2);
        $this->jornada2->save();
    }

    public function test_congelar_alineaciones_service_dispatches_congelar_jornada_job(): void
    {
        Queue::fake();

        app(CongelarAlineacionesService::class)->congelarJornada($this->jornada1);

        Queue::assertPushed(CongelarJornadaJob::class, function (CongelarJornadaJob $job) {
            return $job->jornada->id === $this->jornada1->id;
        });
    }

    public function test_congelar_alineaciones_command_dispatches_job_for_pending_jornadas(): void
    {
        Queue::fake();

        $this->jornada1->fecha_cierre_alineaciones = now()->subMinutes(10);
        $this->jornada1->alineaciones_congeladas = false;
        $this->jornada1->save();

        Artisan::call('fantasy:congelar-alineaciones');

        Queue::assertPushed(CongelarJornadaJob::class, function (CongelarJornadaJob $job) {
            return $job->jornada->id === $this->jornada1->id;
        });
    }

    public function test_congelar_jornada_job_is_idempotent_and_creates_bulk_frozen_lineups(): void
    {
        // Ejecutar Job síncronamente
        CongelarJornadaJob::dispatchSync($this->jornada1);

        $this->assertTrue((bool) $this->jornada1->fresh()->alineaciones_congeladas);

        $alineacionesCount = Alineacion::where('liguilla_id', $this->liguilla->id)
            ->where('jornada_id', $this->jornada1->id)
            ->count();
        $this->assertEquals(2, $alineacionesCount);

        // Reejecutar el Job (comprobar idempotencia)
        CongelarJornadaJob::dispatchSync($this->jornada1);

        $this->assertEquals(
            2,
            Alineacion::where('liguilla_id', $this->liguilla->id)
                ->where('jornada_id', $this->jornada1->id)
                ->count(),
            'El Job debe ser idempotente y no duplicar alineaciones ya congeladas.'
        );
    }

    public function test_congelar_alineaciones_duplica_alineaciones_base_correctamente(): void
    {
        app(CongelarAlineacionesService::class)->congelarJornada($this->jornada1);

        // Comprobar que existen alineaciones congeladas con jornada_id
        $alineacionA = Alineacion::where('user_id', $this->userA->id)
            ->where('liguilla_id', $this->liguilla->id)
            ->where('jornada_id', $this->jornada1->id)
            ->first();

        $alineacionB = Alineacion::where('user_id', $this->userB->id)
            ->where('liguilla_id', $this->liguilla->id)
            ->where('jornada_id', $this->jornada1->id)
            ->first();

        $this->assertNotNull($alineacionA);
        $this->assertNotNull($alineacionB);

        $this->assertCount(2, $alineacionA->jugadores);
        $this->assertCount(2, $alineacionB->jugadores);

        // Puntos inicializados a 0 en el pivot
        foreach ($alineacionA->jugadores as $jug) {
            $this->assertEquals(0, $jug->pivot->puntos);
        }
    }

    public function test_volcar_puntos_actualiza_alineaciones_congeladas_y_totales_de_liguilla(): void
    {
        // 1. Congelar alineaciones para Jornada 1
        app(CongelarAlineacionesService::class)->congelarJornada($this->jornada1);

        // 2. Crear partido jugado en Jornada 1: Equipo 1 gana a Equipo 2 (2-0)
        // Jugador 1 hace 1 gol (+5) + victoria (+3) = 8 pts
        // Jugador 2 victoria (+3) = 3 pts
        // Jugador 3 derrota (0) + 1 amarilla (-3) = -3 pts
        // Jugador 4 derrota (0) = 0 pts
        $partido = new Partido;
        $partido->jornada_id = $this->jornada1->id;
        $partido->equipo_local_id = $this->equipo1->id;
        $partido->equipo_visitante_id = $this->equipo2->id;
        $partido->fecha_partido = now()->subDays(3);
        $partido->goles_local = 2;
        $partido->goles_visitante = 0;
        $partido->estado = 'jugado';
        $partido->eventos = json_encode([
            ['jugador_id' => $this->jugadores[0]->id, 'tipo' => 'Gol'],
            ['jugador_id' => $this->jugadores[2]->id, 'tipo' => 'Tarjeta Amarilla'],
        ]);
        $partido->save();

        $partido->actualizarEstadisticas();

        // 3. Ejecutar volcado de puntos de la jornada a las alineaciones y liguilla
        app(PartidoController::class)->volcarPuntosAJugadoresDeJornada($partido);

        // 4. Verificar puntos en alineacion_jugador
        $alineacionA = Alineacion::where('user_id', $this->userA->id)
            ->where('jornada_id', $this->jornada1->id)
            ->first();

        $puntosA = $alineacionA->jugadores->pluck('pivot.puntos', 'id');
        $this->assertEquals(8, $puntosA[$this->jugadores[0]->id]);
        $this->assertEquals(3, $puntosA[$this->jugadores[1]->id]);

        $alineacionB = Alineacion::where('user_id', $this->userB->id)
            ->where('jornada_id', $this->jornada1->id)
            ->first();

        $puntosB = $alineacionB->jugadores->pluck('pivot.puntos', 'id');
        $this->assertEquals(-3, $puntosB[$this->jugadores[2]->id]);
        $this->assertEquals(0, $puntosB[$this->jugadores[3]->id]);

        // 5. Verificar puntos globales en liguilla_usuario
        $puntosGlobalUserA = $this->liguilla->usuarios()->find($this->userA->id)->pivot->puntos;
        $puntosGlobalUserB = $this->liguilla->usuarios()->find($this->userB->id)->pivot->puntos;

        // User A: 8 + 3 = 11 pts
        // User B: -3 + 0 = -3 pts
        $this->assertEquals(11, $puntosGlobalUserA);
        $this->assertEquals(-3, $puntosGlobalUserB);
    }

    public function test_clasificacion_ajax_modo_global_ordena_correctamente_por_puntos(): void
    {
        // Forzar puntos en pivot liguilla_usuario
        $this->liguilla->usuarios()->updateExistingPivot($this->userA->id, ['puntos' => 25]);
        $this->liguilla->usuarios()->updateExistingPivot($this->userB->id, ['puntos' => 40]);

        $response = $this->actingAs($this->userA)->getJson("/user/liguillas/{$this->liguilla->id}/clasificacion?modo_clasificacion=global");

        $response->assertStatus(200)
            ->assertJson([
                'modo' => 'global',
                'jornada' => null,
            ]);

        $clasificacion = $response->json('clasificacion');
        $this->assertCount(2, $clasificacion);

        // User B debe ser 1º con 40 pts
        $this->assertEquals($this->userB->id, $clasificacion[0]['id']);
        $this->assertEquals(1, $clasificacion[0]['posicion']);
        $this->assertEquals(40, $clasificacion[0]['puntos']);

        // User A debe ser 2º con 25 pts
        $this->assertEquals($this->userA->id, $clasificacion[1]['id']);
        $this->assertEquals(2, $clasificacion[1]['posicion']);
        $this->assertEquals(25, $clasificacion[1]['puntos']);
    }

    public function test_clasificacion_ajax_modo_jornada_calcula_ranking_especifico_de_jornada(): void
    {
        // Crear alineaciones congeladas con puntos para Jornada 1
        $alA = Alineacion::create(['user_id' => $this->userA->id, 'liguilla_id' => $this->liguilla->id, 'jornada_id' => $this->jornada1->id]);
        $alA->jugadores()->attach([
            $this->jugadores[0]->id => ['puntos' => 10],
            $this->jugadores[1]->id => ['puntos' => 5],
        ]); // Total User A: 15 pts

        $alB = Alineacion::create(['user_id' => $this->userB->id, 'liguilla_id' => $this->liguilla->id, 'jornada_id' => $this->jornada1->id]);
        $alB->jugadores()->attach([
            $this->jugadores[2]->id => ['puntos' => 20],
            $this->jugadores[3]->id => ['puntos' => 2],
        ]); // Total User B: 22 pts

        $response = $this->actingAs($this->userA)->getJson("/user/liguillas/{$this->liguilla->id}/clasificacion?modo_clasificacion={$this->jornada1->id}");

        $response->assertStatus(200)
            ->assertJson([
                'modo' => (string) $this->jornada1->id,
                'jornada' => [
                    'id' => $this->jornada1->id,
                    'nombre' => 'Jornada 1',
                    'orden' => 1,
                ],
            ]);

        $clasificacion = $response->json('clasificacion');
        $this->assertCount(2, $clasificacion);

        // User B debe ser 1º con 22 pts
        $this->assertEquals($this->userB->id, $clasificacion[0]['id']);
        $this->assertEquals(1, $clasificacion[0]['posicion']);
        $this->assertEquals(22, $clasificacion[0]['puntos']);

        // User A debe ser 2º con 15 pts
        $this->assertEquals($this->userA->id, $clasificacion[1]['id']);
        $this->assertEquals(2, $clasificacion[1]['posicion']);
        $this->assertEquals(15, $clasificacion[1]['puntos']);
    }

    public function test_alineacion_usuario_jornada_calcula_puntos_por_estadisticas_de_partido(): void
    {
        // Crear alineacion congelada
        $alA = Alineacion::create(['user_id' => $this->userA->id, 'liguilla_id' => $this->liguilla->id, 'jornada_id' => $this->jornada1->id]);
        $alA->jugadores()->attach([$this->jugadores[0]->id, $this->jugadores[1]->id]);

        // Crear partido y estadisticas para la Jornada 1
        $partido = new Partido;
        $partido->jornada_id = $this->jornada1->id;
        $partido->equipo_local_id = $this->equipo1->id;
        $partido->equipo_visitante_id = $this->equipo2->id;
        $partido->fecha_partido = now()->subDays(3);
        $partido->goles_local = 1;
        $partido->goles_visitante = 0;
        $partido->estado = 'jugado';
        $partido->save();

        Estadistica::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugadores[0]->id,
            'goles' => 1,
            'puntos' => 8, // 3 victoria + 5 gol
            'resultado' => 'ganado',
        ]);

        Estadistica::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugadores[1]->id,
            'puntos' => 3, // 3 victoria
            'resultado' => 'ganado',
        ]);

        $response = $this->actingAs($this->userA)->getJson("/user/liguillas/{$this->liguilla->id}/alineacion-usuario/{$this->userA->id}/jornada/{$this->jornada1->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'total_puntos' => 11,
            ])
            ->assertJsonCount(2, 'jugadores')
            ->assertJsonFragment([
                'id' => $this->jugadores[0]->id,
                'puntos' => 8,
            ])
            ->assertJsonFragment([
                'id' => $this->jugadores[1]->id,
                'puntos' => 3,
            ]);
    }

    public function test_alineacion_usuario_jornada_retorna_vacio_si_no_existe_alineacion(): void
    {
        $response = $this->actingAs($this->userA)->getJson("/user/liguillas/{$this->liguilla->id}/alineacion-usuario/{$this->userA->id}/jornada/{$this->jornada2->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'jugadores' => [],
                'total_puntos' => 0,
            ]);
    }

    public function test_usuario_fuera_de_liguilla_no_puede_ver_plantilla_de_participante_por_idor(): void
    {
        $usuarioExterno = User::factory()->create(['name' => 'Usuario Externo', 'email' => 'externo@test.com', 'active' => true]);

        $response = $this->actingAs($usuarioExterno)->get("/user/liguillas/{$this->liguilla->id}/usuario/{$this->userA->id}/plantilla");

        $response->assertStatus(403);
    }

    public function test_usuario_miembro_de_liguilla_puede_ver_plantilla_de_participante(): void
    {
        $response = $this->actingAs($this->userB)->get("/user/liguillas/{$this->liguilla->id}/usuario/{$this->userA->id}/plantilla");

        $response->assertStatus(200)
            ->assertViewIs('user.plantilla-participante')
            ->assertViewHas('plantilla');
    }

    public function test_actualizar_resultado_dispatches_recalcular_puntos_jornada_job(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['name' => 'Admin', 'email' => 'admin@test.com', 'active' => true, 'admin' => true]);

        $partido = new Partido;
        $partido->jornada_id = $this->jornada1->id;
        $partido->equipo_local_id = $this->equipo1->id;
        $partido->equipo_visitante_id = $this->equipo2->id;
        $partido->fecha_partido = now()->subDays(1);
        $partido->goles_local = 2;
        $partido->goles_visitante = 1;
        $partido->estado = 'jugado';
        $partido->save();

        $response = $this->actingAs($admin)->post('/admin/partidos/actualizar-resultado', [
            'partido_id' => $partido->id,
            'goles_local' => 3,
            'goles_visitante' => 1,
            'estado' => 'jugado', // from select
        ]);

        $response->assertRedirect();

        Queue::assertPushed(RecalcularPuntosJornadaJob::class, function ($job) use ($partido) {
            return $job->jornadaId === $partido->jornada_id;
        });
    }

    public function test_recalcular_puntos_jornada_job_is_idempotent_and_calculates_bulk_points(): void
    {
        // Set up alignments and stats directly
        $alA = Alineacion::create(['user_id' => $this->userA->id, 'liguilla_id' => $this->liguilla->id, 'jornada_id' => $this->jornada1->id]);
        $alA->jugadores()->attach([
            $this->jugadores[0]->id => ['puntos' => 0],
            $this->jugadores[1]->id => ['puntos' => 0],
        ]);

        $partido = new Partido;
        $partido->jornada_id = $this->jornada1->id;
        $partido->equipo_local_id = $this->equipo1->id;
        $partido->equipo_visitante_id = $this->equipo2->id;
        $partido->fecha_partido = now()->subDays(1);
        $partido->estado = 'jugado';
        $partido->save();
        $partidoId = $partido->id;

        Estadistica::create(['partido_id' => $partidoId, 'jugador_id' => $this->jugadores[0]->id, 'puntos' => 6, 'goles' => 1, 'resultado' => 'empatado']);
        Estadistica::create(['partido_id' => $partidoId, 'jugador_id' => $this->jugadores[1]->id, 'puntos' => 4, 'asistencias' => 1, 'resultado' => 'empatado']);

        // First run
        $job = new RecalcularPuntosJornadaJob($this->jornada1->id);
        $job->handle();

        $puntosPivot = DB::table('alineacion_jugador')->where('alineacion_id', $alA->id)->pluck('puntos', 'jugador_id');
        $this->assertEquals(6, $puntosPivot[$this->jugadores[0]->id]);
        $this->assertEquals(4, $puntosPivot[$this->jugadores[1]->id]);

        $ligGlobalA = DB::table('liguilla_usuario')->where('liguilla_id', $this->liguilla->id)->where('user_id', $this->userA->id)->first();
        $this->assertEquals(10, $ligGlobalA->puntos);

        // Edit stats directly
        Estadistica::where('jugador_id', $this->jugadores[0]->id)->update(['puntos' => 10]);

        // Second run (Idempotency + Bulk updates catching new numbers correctly without appending)
        $job->handle();

        $puntosPivot2 = DB::table('alineacion_jugador')->where('alineacion_id', $alA->id)->pluck('puntos', 'jugador_id');
        $this->assertEquals(10, $puntosPivot2[$this->jugadores[0]->id]);
        $this->assertEquals(4, $puntosPivot2[$this->jugadores[1]->id]);

        $ligGlobalA2 = DB::table('liguilla_usuario')->where('liguilla_id', $this->liguilla->id)->where('user_id', $this->userA->id)->first();
        $this->assertEquals(14, $ligGlobalA2->puntos);
    }
}
