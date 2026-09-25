<?php

namespace Tests\Feature;

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
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_all_entities_and_relationships_correctly(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Usuarios
        $this->assertDatabaseCount('users', 5);
        $admin = User::where('email', 'admin@admin.es')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->admin);
        $this->assertTrue($admin->active);

        // 2. Equipos
        $this->assertDatabaseCount('equipos', 20);

        // 3. Jugadores
        $this->assertGreaterThanOrEqual(100, Jugador::count());
        $this->assertDatabaseCount('equipo_jugador', Jugador::count());

        // 4. Torneo y Jornadas
        $this->assertDatabaseCount('torneos', 1);
        $torneo = Torneo::first();
        $this->assertEquals('activo', $torneo->estado);
        $this->assertEquals(5, $torneo->jugadores_por_equipo);
        $this->assertDatabaseCount('jornadas', 4);
        $this->assertDatabaseCount('equipo_torneo', 20);
        $this->assertDatabaseCount('equipo_jugador_torneo', Jugador::count());

        // 5. Partidos y Estadísticas
        $this->assertDatabaseCount('partidos', 40);
        $partidosJugados = Partido::where('estado', 'jugado')->count();
        $this->assertEquals(20, $partidosJugados);
        $this->assertGreaterThan(0, Estadistica::count());

        // 6. Liguilla y Usuarios
        $this->assertDatabaseCount('liguillas', 1);
        $liguilla = Liguilla::first();
        $this->assertEquals('activa', $liguilla->estado);
        $this->assertDatabaseCount('liguilla_usuario', 5);

        // 7. Plantillas y Jugadores en Plantilla
        $this->assertDatabaseCount('plantillas', 5);
        $this->assertDatabaseCount('jugador_plantilla', 40); // 5 usuarios * 8 jugadores

        // 8. Alineaciones (Base + J1 + J2 + J3 = 4 por usuario = 20 total)
        $this->assertDatabaseCount('alineaciones', 20);
        $this->assertDatabaseCount('alineacion_jugador', 100); // 20 alineaciones * 5 titulares

        // 9. Puntos y Clasificación
        $puntosUsuarios = DB::table('liguilla_usuario')
            ->where('liguilla_id', $liguilla->id)
            ->pluck('puntos', 'user_id');

        $this->assertCount(5, $puntosUsuarios);
        // Verificar que hay puntos calculados y no están todos a cero
        $this->assertGreaterThan(0, $puntosUsuarios->sum());

        // Verificar que los puestos están asignados del 1 al 5
        $puestos = DB::table('liguilla_usuario')
            ->where('liguilla_id', $liguilla->id)
            ->pluck('puesto')
            ->sort()
            ->values()
            ->all();

        $this->assertEquals([1, 2, 3, 4, 5], $puestos);
    }
}
