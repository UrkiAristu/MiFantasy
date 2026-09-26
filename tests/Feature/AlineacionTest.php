<?php

namespace Tests\Feature;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlineacionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Torneo $torneoSala;
    private Liguilla $liguillaSala;
    private Plantilla $plantillaSala;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        // 1. Crear usuario
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'active' => true,
        ]);

        // 2. Crear torneo de fútbol sala por defecto
        $this->torneoSala = Torneo::factory()->sala()->activo()->create([
            'nombre' => 'Liga Sala Test',
        ]);

        // 3. Crear Liguilla
        $this->liguillaSala = Liguilla::factory()->activa()->create([
            'nombre' => 'Liguilla Sala Test',
            'torneo_id' => $this->torneoSala->id,
            'creador_id' => $this->user->id,
        ]);

        // Unir al usuario a la liguilla
        $this->liguillaSala->usuarios()->attach($this->user->id, ['puntos' => 0]);

        // 4. Crear plantilla del usuario
        $this->plantillaSala = Plantilla::create([
            'liguilla_id' => $this->liguillaSala->id,
            'user_id' => $this->user->id,
        ]);
    }

    private function crearJugador(string $posicion): Jugador
    {
        return Jugador::factory()->create([
            'posicion' => $posicion,
        ]);
    }

    private function asignarAPlantilla(Plantilla $plantilla, array $jugadores): void
    {
        foreach ($jugadores as $j) {
            $plantilla->jugadores()->attach($j->id);
        }
    }

    public function test_usuario_no_autenticado_no_puede_guardar_alineacion(): void
    {
        $jugador = $this->crearJugador('Portero');
        $this->asignarAPlantilla($this->plantillaSala, [$jugador]);

        $response = $this->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => [$jugador->id],
            'formacion' => '1-2-1',
        ]);

        $response->assertStatus(401);
    }

    public function test_usuario_fuera_de_la_liguilla_no_puede_guardar_alineacion(): void
    {
        $otroUsuario = User::factory()->create();
        $jugador = $this->crearJugador('Portero');
        $this->asignarAPlantilla($this->plantillaSala, [$jugador]);

        $response = $this->actingAs($otroUsuario)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => [$jugador->id],
            'formacion' => '1-2-1',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status'  => 'error',
                'message' => 'No puedes modificar alineaciones de una liguilla en la que no participas.',
            ]);
    }

    public function test_no_se_pueden_alinear_jugadores_que_no_pertenecen_a_la_plantilla(): void
    {
        $jugadorExterno = $this->crearJugador('Delantero');

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => [$jugadorExterno->id],
            'formacion' => '1-2-1',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status'  => 'error',
                'message' => 'Solo puedes alinear jugadores que están en tu plantilla.',
            ]);
    }

    public function test_no_se_puede_guardar_formacion_invalida_para_la_modalidad(): void
    {
        $jugador = $this->crearJugador('Portero');
        $this->asignarAPlantilla($this->plantillaSala, [$jugador]);

        // '4-4-2' no existe para modalidad 'sala'
        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => [$jugador->id],
            'formacion' => '4-4-2',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status'  => 'error',
                'message' => "La formación '4-4-2' no es válida para la modalidad 'sala'.",
            ]);
    }

    public function test_no_se_pueden_alinear_mas_jugadores_del_limite_de_la_formacion(): void
    {
        // En fútbol sala con '1-2-1', el límite total es 5 jugadores. Intentamos alinear 6.
        $jugadores = [
            $this->crearJugador('Portero'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Delantero'),
            $this->crearJugador('Delantero'),
        ];
        $this->asignarAPlantilla($this->plantillaSala, $jugadores);

        $ids = array_map(fn($j) => $j->id, $jugadores);

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => $ids,
            'formacion' => '1-2-1',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status'  => 'error',
                'message' => 'Solo puedes seleccionar hasta 5 jugadores para la formación 1-2-1.',
            ]);
    }

    public function test_no_se_pueden_superar_los_topes_por_posicion_segun_la_formacion(): void
    {
        // En formación '1-2-1' (sala): Portero: 1, Defensa: 1, Centrocampista: 2, Delantero: 1.
        // Intentamos poner 2 Delanteros (total 5 jugadores, pero cuota de Delanteros superada).
        $jugadores = [
            $this->crearJugador('Portero'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Delantero'),
            $this->crearJugador('Delantero'),
        ];
        $this->asignarAPlantilla($this->plantillaSala, $jugadores);

        $ids = array_map(fn($j) => $j->id, $jugadores);

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => $ids,
            'formacion' => '1-2-1',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status'  => 'error',
                'message' => "Has seleccionado 2 jugadores para la posición 'Delantero', pero la formación 1-2-1 solo permite un máximo de 1.",
            ]);
    }

    public function test_no_se_puede_superar_el_tope_de_porteros(): void
    {
        // En formación '1-2-1' (sala): Portero: 1.
        // Intentamos poner 2 Porteros.
        $jugadores = [
            $this->crearJugador('Portero'),
            $this->crearJugador('Portero'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Centrocampista'),
        ];
        $this->asignarAPlantilla($this->plantillaSala, $jugadores);

        $ids = array_map(fn($j) => $j->id, $jugadores);

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => $ids,
            'formacion' => '1-2-1',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status'  => 'error',
                'message' => "Has seleccionado 2 jugadores para la posición 'Portero', pero la formación 1-2-1 solo permite un máximo de 1.",
            ]);
    }

    public function test_guardar_alineacion_base_con_formacion_valida_en_futbol_sala(): void
    {
        $jugadores = [
            $this->crearJugador('Portero'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Delantero'),
        ];
        $this->asignarAPlantilla($this->plantillaSala, $jugadores);
        $ids = array_map(fn($j) => $j->id, $jugadores);

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => $ids,
            'formacion' => '1-2-1',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'    => 'success',
                'message'   => 'Alineación guardada correctamente',
                'formacion' => '1-2-1',
            ]);

        // Verificar persistencia en base de datos
        $alineacionBase = Alineacion::where('user_id', $this->user->id)
            ->where('liguilla_id', $this->liguillaSala->id)
            ->whereNull('jornada_id')
            ->first();

        $this->assertNotNull($alineacionBase);
        $this->assertEquals('1-2-1', $alineacionBase->formacion);
        $this->assertCount(5, $alineacionBase->jugadores);
        $this->assertEqualsCanonicalizing($ids, $alineacionBase->jugadores->pluck('id')->toArray());
    }

    public function test_guardar_alineacion_base_en_futbol_7(): void
    {
        $torneo7 = Torneo::factory()->f7()->activo()->create(['nombre' => 'Liga F7']);
        $liguilla7 = Liguilla::factory()->activa()->create([
            'torneo_id' => $torneo7->id,
            'creador_id' => $this->user->id,
        ]);
        $liguilla7->usuarios()->attach($this->user->id, ['puntos' => 0]);
        $plantilla7 = Plantilla::create([
            'liguilla_id' => $liguilla7->id,
            'user_id' => $this->user->id,
        ]);

        // Formación '3-2-1': 1 Portero, 3 Defensas, 2 Centrocampistas, 1 Delantero (Total: 7)
        $jugadores = [
            $this->crearJugador('Portero'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Delantero'),
        ];
        $this->asignarAPlantilla($plantilla7, $jugadores);
        $ids = array_map(fn($j) => $j->id, $jugadores);

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$liguilla7->id}/alineacion/guardar", [
            'jugadores' => $ids,
            'formacion' => '3-2-1',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'    => 'success',
                'message'   => 'Alineación guardada correctamente',
                'formacion' => '3-2-1',
            ]);

        $alineacion = Alineacion::where('user_id', $this->user->id)
            ->where('liguilla_id', $liguilla7->id)
            ->whereNull('jornada_id')
            ->first();

        $this->assertNotNull($alineacion);
        $this->assertEquals('3-2-1', $alineacion->formacion);
        $this->assertCount(7, $alineacion->jugadores);
    }

    public function test_guardar_alineacion_base_en_futbol_11(): void
    {
        $torneo11 = Torneo::factory()->f11()->activo()->create(['nombre' => 'Liga F11']);
        $liguilla11 = Liguilla::factory()->activa()->create([
            'torneo_id' => $torneo11->id,
            'creador_id' => $this->user->id,
        ]);
        $liguilla11->usuarios()->attach($this->user->id, ['puntos' => 0]);
        $plantilla11 = Plantilla::create([
            'liguilla_id' => $liguilla11->id,
            'user_id' => $this->user->id,
        ]);

        // Formación '4-3-3': 1 Portero, 4 Defensas, 3 Centrocampistas, 3 Delanteros (Total: 11)
        $jugadores = [
            $this->crearJugador('Portero'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Defensa'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Centrocampista'),
            $this->crearJugador('Delantero'),
            $this->crearJugador('Delantero'),
            $this->crearJugador('Delantero'),
        ];
        $this->asignarAPlantilla($plantilla11, $jugadores);
        $ids = array_map(fn($j) => $j->id, $jugadores);

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$liguilla11->id}/alineacion/guardar", [
            'jugadores' => $ids,
            'formacion' => '4-3-3',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'    => 'success',
                'message'   => 'Alineación guardada correctamente',
                'formacion' => '4-3-3',
            ]);

        $alineacion = Alineacion::where('user_id', $this->user->id)
            ->where('liguilla_id', $liguilla11->id)
            ->whereNull('jornada_id')
            ->first();

        $this->assertNotNull($alineacion);
        $this->assertEquals('4-3-3', $alineacion->formacion);
        $this->assertCount(11, $alineacion->jugadores);
    }

    public function test_cambio_de_tactica_actualiza_formacion_existente(): void
    {
        // 1. Guardar primero en '1-2-1'
        $j1 = $this->crearJugador('Portero');
        $j2 = $this->crearJugador('Defensa');
        $j3 = $this->crearJugador('Centrocampista');
        $this->asignarAPlantilla($this->plantillaSala, [$j1, $j2, $j3]);

        $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => [$j1->id, $j2->id, $j3->id],
            'formacion' => '1-2-1',
        ])->assertStatus(200);

        // 2. Cambiar a '2-2' (1 Portero, 2 Defensas, 0 Centros, 2 Delanteros)
        $defensa2 = $this->crearJugador('Defensa');
        $delantero1 = $this->crearJugador('Delantero');
        $delantero2 = $this->crearJugador('Delantero');
        $this->asignarAPlantilla($this->plantillaSala, [$defensa2, $delantero1, $delantero2]);

        $nuevosIds = [$j1->id, $j2->id, $defensa2->id, $delantero1->id, $delantero2->id];

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/guardar", [
            'jugadores' => $nuevosIds,
            'formacion' => '2-2',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'    => 'success',
                'formacion' => '2-2',
            ]);

        $alineacion = Alineacion::where('user_id', $this->user->id)
            ->where('liguilla_id', $this->liguillaSala->id)
            ->whereNull('jornada_id')
            ->first();

        $this->assertEquals('2-2', $alineacion->formacion);
        $this->assertCount(5, $alineacion->jugadores);
    }

    public function test_obtener_alineacion_retorna_empty_si_no_existe_para_la_jornada(): void
    {
        $jornada = Jornada::factory()->create([
            'torneo_id' => $this->torneoSala->id,
            'orden' => 1,
        ]);

        $response = $this->actingAs($this->user)->getJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/{$jornada->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status'       => 'empty',
                'formacion'    => null,
                'jugadores'    => [],
                'total_puntos' => 0,
            ]);
    }

    public function test_obtener_alineacion_retorna_datos_y_puntos_si_existe_para_la_jornada(): void
    {
        $jornada = Jornada::factory()->create([
            'torneo_id' => $this->torneoSala->id,
            'orden' => 1,
        ]);

        $portero = $this->crearJugador('Portero');
        $defensa = $this->crearJugador('Defensa');

        // Crear alineación congelada para la jornada
        $alineacion = Alineacion::create([
            'user_id'     => $this->user->id,
            'liguilla_id' => $this->liguillaSala->id,
            'jornada_id'  => $jornada->id,
            'formacion'   => '1-2-1',
        ]);

        $alineacion->jugadores()->attach([
            $portero->id => ['puntos' => 8],
            $defensa->id => ['puntos' => 4],
        ]);

        $response = $this->actingAs($this->user)->getJson("/user/liguillas/{$this->liguillaSala->id}/alineacion/{$jornada->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status'       => 'ok',
                'formacion'    => '1-2-1',
                'total_puntos' => 12,
            ])
            ->assertJsonCount(2, 'jugadores')
            ->assertJsonFragment([
                'id'     => $portero->id,
                'nombre' => $portero->nombre,
                'puntos' => 8,
            ])
            ->assertJsonFragment([
                'id'     => $defensa->id,
                'nombre' => $defensa->nombre,
                'puntos' => 4,
            ]);
    }
}
