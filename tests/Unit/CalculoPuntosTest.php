<?php

namespace Tests\Unit;

use App\Models\Equipo;
use App\Models\Estadistica;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CalculoPuntosTest extends TestCase
{
    use RefreshDatabase;

    private Torneo $torneo;
    private Equipo $equipoLocal;
    private Equipo $equipoVisitante;
    private Jugador $jugadorLocal;
    private Jugador $jugadorVisitante;
    private Jornada $jornada;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Crear torneo
        $this->torneo = new Torneo();
        $this->torneo->nombre = 'Torneo Test';
        $this->torneo->fecha_inicio = now()->subDays(5);
        $this->torneo->fecha_fin = now()->addDays(5);
        $this->torneo->jugadores_por_equipo = 5;
        $this->torneo->save();

        // 2. Crear equipos
        $this->equipoLocal = new Equipo();
        $this->equipoLocal->nombre = 'Equipo Local';
        $this->equipoLocal->save();

        $this->equipoVisitante = new Equipo();
        $this->equipoVisitante->nombre = 'Equipo Visitante';
        $this->equipoVisitante->save();

        // 3. Crear jugadores
        $this->jugadorLocal = new Jugador();
        $this->jugadorLocal->nombre = 'Juan';
        $this->jugadorLocal->apellido1 = 'Perez';
        $this->jugadorLocal->save();

        $this->jugadorVisitante = new Jugador();
        $this->jugadorVisitante->nombre = 'Carlos';
        $this->jugadorVisitante->apellido1 = 'Gomez';
        $this->jugadorVisitante->save();

        // Inscribir jugadores en los equipos para este torneo
        DB::table('equipo_jugador_torneo')->insert([
            [
                'jugador_id' => $this->jugadorLocal->id,
                'equipo_id'  => $this->equipoLocal->id,
                'torneo_id'  => $this->torneo->id,
                'goles'      => 0,
                'asistencias'=> 0,
                'puntos'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jugador_id' => $this->jugadorVisitante->id,
                'equipo_id'  => $this->equipoVisitante->id,
                'torneo_id'  => $this->torneo->id,
                'goles'      => 0,
                'asistencias'=> 0,
                'puntos'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Crear jornada
        $this->jornada = new Jornada();
        $this->jornada->torneo_id = $this->torneo->id;
        $this->jornada->nombre = 'Jornada 1';
        $this->jornada->orden = 1;
        $this->jornada->fecha_inicio = now()->subDay();
        $this->jornada->fecha_fin = now()->addDay();
        $this->jornada->save();
    }

    public function test_victoria_local_asigna_3_puntos_a_local_y_0_a_visitante(): void
    {
        $partido = new Partido();
        $partido->jornada_id = $this->jornada->id;
        $partido->equipo_local_id = $this->equipoLocal->id;
        $partido->equipo_visitante_id = $this->equipoVisitante->id;
        $partido->fecha_partido = now();
        $partido->goles_local = 2;
        $partido->goles_visitante = 1;
        $partido->estado = 'jugado';
        $partido->save();

        $partido->actualizarEstadisticas();

        $statLocal = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorLocal->id)
            ->first();

        $statVisitante = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorVisitante->id)
            ->first();

        $this->assertNotNull($statLocal);
        $this->assertEquals('ganado', $statLocal->resultado);
        $this->assertEquals(3, $statLocal->puntos);

        $this->assertNotNull($statVisitante);
        $this->assertEquals('perdido', $statVisitante->resultado);
        $this->assertEquals(0, $statVisitante->puntos);
    }

    public function test_empate_asigna_1_punto_a_ambos_equipos(): void
    {
        $partido = new Partido();
        $partido->jornada_id = $this->jornada->id;
        $partido->equipo_local_id = $this->equipoLocal->id;
        $partido->equipo_visitante_id = $this->equipoVisitante->id;
        $partido->fecha_partido = now();
        $partido->goles_local = 1;
        $partido->goles_visitante = 1;
        $partido->estado = 'jugado';
        $partido->save();

        $partido->actualizarEstadisticas();

        $statLocal = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorLocal->id)
            ->first();

        $statVisitante = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorVisitante->id)
            ->first();

        $this->assertNotNull($statLocal);
        $this->assertEquals('empatado', $statLocal->resultado);
        $this->assertEquals(1, $statLocal->puntos);

        $this->assertNotNull($statVisitante);
        $this->assertEquals('empatado', $statVisitante->resultado);
        $this->assertEquals(1, $statVisitante->puntos);
    }

    public function test_victoria_visitante_asigna_3_puntos_a_visitante_y_0_a_local(): void
    {
        $partido = new Partido();
        $partido->jornada_id = $this->jornada->id;
        $partido->equipo_local_id = $this->equipoLocal->id;
        $partido->equipo_visitante_id = $this->equipoVisitante->id;
        $partido->fecha_partido = now();
        $partido->goles_local = 0;
        $partido->goles_visitante = 3;
        $partido->estado = 'jugado';
        $partido->save();

        $partido->actualizarEstadisticas();

        $statLocal = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorLocal->id)
            ->first();

        $statVisitante = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorVisitante->id)
            ->first();

        $this->assertEquals('perdido', $statLocal->resultado);
        $this->assertEquals(0, $statLocal->puntos);

        $this->assertEquals('ganado', $statVisitante->resultado);
        $this->assertEquals(3, $statVisitante->puntos);
    }

    public function test_modificadores_de_eventos_suman_y_restan_puntos_correctamente(): void
    {
        // El jugador local gana (3 pts) y tiene:
        // - 2 Goles (+10 pts)
        // - 1 Asistencia (+3 pts)
        // - 1 Parada (+2 pts)
        // - 1 Tarjeta Amarilla (-3 pts)
        // - 1 Falta (-1 pt)
        // Total esperado: 3 + 10 + 3 + 2 - 3 - 1 = 14 pts
        $eventos = [
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Gol'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Gol'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Asistencia'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Parada'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Tarjeta Amarilla'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Falta'],
            // El jugador visitante pierde (0 pts) y tiene:
            // - 1 Tarjeta Roja (-5 pts)
            // Total esperado: 0 - 5 = -5 pts
            ['jugador_id' => $this->jugadorVisitante->id, 'tipo' => 'Tarjeta Roja'],
        ];

        $partido = new Partido();
        $partido->jornada_id = $this->jornada->id;
        $partido->equipo_local_id = $this->equipoLocal->id;
        $partido->equipo_visitante_id = $this->equipoVisitante->id;
        $partido->fecha_partido = now();
        $partido->goles_local = 2;
        $partido->goles_visitante = 0;
        $partido->estado = 'jugado';
        $partido->eventos = json_encode($eventos);
        $partido->save();

        $partido->actualizarEstadisticas();

        $statLocal = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorLocal->id)
            ->first();

        $statVisitante = Estadistica::where('partido_id', $partido->id)
            ->where('jugador_id', $this->jugadorVisitante->id)
            ->first();

        $this->assertEquals(2, $statLocal->goles);
        $this->assertEquals(1, $statLocal->asistencias);
        $this->assertEquals(1, $statLocal->paradas);
        $this->assertEquals(1, $statLocal->tarjetas_amarillas);
        $this->assertEquals(1, $statLocal->faltas);
        $this->assertEquals(14, $statLocal->puntos);

        $this->assertEquals(1, $statVisitante->tarjetas_rojas);
        $this->assertEquals(-5, $statVisitante->puntos);
    }

    public function test_recalcular_estadisticas_limpia_registros_previos(): void
    {
        $partido = new Partido();
        $partido->jornada_id = $this->jornada->id;
        $partido->equipo_local_id = $this->equipoLocal->id;
        $partido->equipo_visitante_id = $this->equipoVisitante->id;
        $partido->fecha_partido = now();
        $partido->goles_local = 1;
        $partido->goles_visitante = 0;
        $partido->estado = 'jugado';
        $partido->eventos = json_encode([
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Gol'],
        ]);
        $partido->save();

        $partido->actualizarEstadisticas();
        $this->assertCount(2, Estadistica::where('partido_id', $partido->id)->get());

        // Modificamos resultado y volvemos a calcular
        $partido->goles_local = 0;
        $partido->goles_visitante = 0;
        $partido->eventos = json_encode([]);
        $partido->save();

        $partido->actualizarEstadisticas();

        // No debe haber duplicados, debe haber exactamente 2 stats con 1 punto cada uno (empate)
        $stats = Estadistica::where('partido_id', $partido->id)->get();
        $this->assertCount(2, $stats);

        $statLocal = $stats->firstWhere('jugador_id', $this->jugadorLocal->id);
        $this->assertEquals(0, $statLocal->goles);
        $this->assertEquals(1, $statLocal->puntos);
        $this->assertEquals('empatado', $statLocal->resultado);
    }

    public function test_resumen_estadisticas_en_torneo_calcula_agregaciones_sql_correctamente(): void
    {
        // Crear un partido con estadísticas para el jugador local
        $partido = new Partido();
        $partido->jornada_id = $this->jornada->id;
        $partido->equipo_local_id = $this->equipoLocal->id;
        $partido->equipo_visitante_id = $this->equipoVisitante->id;
        $partido->fecha_partido = now();
        $partido->goles_local = 2;
        $partido->goles_visitante = 1;
        $partido->estado = 'jugado';
        $partido->eventos = json_encode([
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Gol'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Gol'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Asistencia'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Parada'],
            ['jugador_id' => $this->jugadorLocal->id, 'tipo' => 'Tarjeta Amarilla'],
        ]);
        $partido->save();
        $partido->actualizarEstadisticas();

        $resumen = $this->jugadorLocal->resumenEstadisticasEnTorneo($this->torneo->id);

        $this->assertEquals(1, $resumen['partidos_jugados']);
        $this->assertEquals(2, $resumen['goles']);
        $this->assertEquals(1, $resumen['asistencias']);
        $this->assertEquals(1, $resumen['paradas']);
        $this->assertEquals(1, $resumen['amarillas']);
        $this->assertEquals(0, $resumen['rojas']);
        $this->assertEquals(15, $resumen['puntos']); // 3 victoria + 10 goles + 3 asistencia + 2 parada - 3 amarilla = 15
    }

    public function test_equipo_en_torneo_retorna_equipo_y_memoiza(): void
    {
        $equipo = $this->jugadorLocal->equipoEnTorneo($this->torneo->id);
        $this->assertNotNull($equipo);
        $this->assertEquals($this->equipoLocal->id, $equipo->id);
        $this->assertEquals($this->equipoLocal->nombre, $equipo->nombre);

        // Probar que usando participaciones pre-cargadas también funciona
        $jugadorConRelacion = Jugador::with('participaciones')->find($this->jugadorLocal->id);
        $equipoPreCargado = $jugadorConRelacion->equipoEnTorneo($this->torneo->id);
        $this->assertNotNull($equipoPreCargado);
        $this->assertEquals($this->equipoLocal->id, $equipoPreCargado->id);
    }

    public function test_estadisticas_restriccion_unica_partido_jugador(): void
    {
        $partido = new Partido();
        $partido->jornada_id = $this->jornada->id;
        $partido->equipo_local_id = $this->equipoLocal->id;
        $partido->equipo_visitante_id = $this->equipoVisitante->id;
        $partido->fecha_partido = now();
        $partido->save();

        Estadistica::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugadorLocal->id,
            'puntos'     => 3,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Estadistica::create([
            'partido_id' => $partido->id,
            'jugador_id' => $this->jugadorLocal->id,
            'puntos'     => 5,
        ]);
    }
}
