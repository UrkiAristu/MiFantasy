<?php

namespace Tests\Feature;

use App\Models\Alineacion;
use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AlineacionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Torneo $torneo;
    private Liguilla $liguilla;
    private Plantilla $plantilla;
    private array $jugadores;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        // 1. Crear usuario
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'active' => true,
        ]);

        // 2. Crear torneo con límite de 3 jugadores por equipo
        $this->torneo = new Torneo();
        $this->torneo->nombre = 'Torneo 3v3';
        $this->torneo->jugadores_por_equipo = 3;
        $this->torneo->fecha_inicio = now()->subDays(2);
        $this->torneo->fecha_fin = now()->addDays(10);
        $this->torneo->save();

        // 3. Crear Liguilla
        $this->liguilla = new Liguilla();
        $this->liguilla->nombre = 'Liguilla Test';
        $this->liguilla->torneo_id = $this->torneo->id;
        $this->liguilla->max_usuarios = 10;
        $this->liguilla->codigo_unico = 'LIGTEST1';
        $this->liguilla->creador_id = $this->user->id;
        $this->liguilla->save();

        // Unir al usuario a la liguilla
        $this->liguilla->usuarios()->attach($this->user->id, ['puntos' => 0]);

        // 4. Crear jugadores y asignarlos a la plantilla del usuario
        $this->plantilla = Plantilla::create([
            'liguilla_id' => $this->liguilla->id,
            'user_id' => $this->user->id,
        ]);

        $this->jugadores = [];
        for ($i = 1; $i <= 5; $i++) {
            $jugador = new Jugador();
            $jugador->nombre = "Jugador $i";
            $jugador->apellido1 = "Apellido $i";
            $jugador->save();

            $this->jugadores[] = $jugador;

            // Añadir a la plantilla
            $this->plantilla->jugadores()->attach($jugador->id);
        }
    }

    public function test_usuario_no_autenticado_no_puede_guardar_alineacion(): void
    {
        $response = $this->postJson("/user/liguillas/{$this->liguilla->id}/alineacion/guardar", [
            'jugadores' => [$this->jugadores[0]->id, $this->jugadores[1]->id],
        ]);

        $response->assertStatus(401);
    }

    public function test_usuario_fuera_de_la_liguilla_no_puede_guardar_alineacion(): void
    {
        $otroUsuario = User::factory()->create();

        $response = $this->actingAs($otroUsuario)->postJson("/user/liguillas/{$this->liguilla->id}/alineacion/guardar", [
            'jugadores' => [$this->jugadores[0]->id],
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status'  => 'error',
                'message' => 'No puedes modificar alineaciones de una liguilla en la que no participas.',
            ]);
    }

    public function test_no_se_pueden_alinear_mas_jugadores_del_limite_del_torneo(): void
    {
        // El límite es 3, intentamos alinear 4 jugadores
        $ids = array_map(fn($j) => $j->id, array_slice($this->jugadores, 0, 4));

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguilla->id}/alineacion/guardar", [
            'jugadores' => $ids,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status'  => 'error',
                'message' => 'Solo puedes seleccionar hasta 3 jugadores.',
            ]);
    }

    public function test_no_se_pueden_alinear_jugadores_que_no_pertenecen_a_la_plantilla(): void
    {
        $jugadorExterno = new Jugador();
        $jugadorExterno->nombre = 'Jugador Externo';
        $jugadorExterno->save();

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguilla->id}/alineacion/guardar", [
            'jugadores' => [$jugadorExterno->id],
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status'  => 'error',
                'message' => 'Solo puedes alinear jugadores que están en tu plantilla.',
            ]);
    }

    public function test_guardar_alineacion_base_exitosamente(): void
    {
        $idsSeleccionados = [$this->jugadores[0]->id, $this->jugadores[1]->id, $this->jugadores[2]->id];

        $response = $this->actingAs($this->user)->postJson("/user/liguillas/{$this->liguilla->id}/alineacion/guardar", [
            'jugadores' => $idsSeleccionados,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'  => 'success',
                'message' => 'Alineación guardada correctamente',
            ]);

        // Verificar que se creó la alineación base con jornada_id null
        $alineacionBase = Alineacion::where('user_id', $this->user->id)
            ->where('liguilla_id', $this->liguilla->id)
            ->whereNull('jornada_id')
            ->first();

        $this->assertNotNull($alineacionBase);
        $this->assertCount(3, $alineacionBase->jugadores);
        $this->assertEqualsCanonicalizing($idsSeleccionados, $alineacionBase->jugadores->pluck('id')->toArray());
    }

    public function test_obtener_alineacion_retorna_empty_si_no_existe_para_la_jornada(): void
    {
        $jornada = new Jornada();
        $jornada->torneo_id = $this->torneo->id;
        $jornada->nombre = 'Jornada 1';
        $jornada->orden = 1;
        $jornada->save();

        $response = $this->actingAs($this->user)->getJson("/user/liguillas/{$this->liguilla->id}/alineacion/{$jornada->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status'       => 'empty',
                'jugadores'    => [],
                'total_puntos' => 0,
            ]);
    }

    public function test_obtener_alineacion_retorna_datos_y_puntos_si_existe_para_la_jornada(): void
    {
        $jornada = new Jornada();
        $jornada->torneo_id = $this->torneo->id;
        $jornada->nombre = 'Jornada 1';
        $jornada->orden = 1;
        $jornada->save();

        // Crear alineación congelada para la jornada
        $alineacion = Alineacion::create([
            'user_id'     => $this->user->id,
            'liguilla_id' => $this->liguilla->id,
            'jornada_id'  => $jornada->id,
        ]);

        $alineacion->jugadores()->attach([
            $this->jugadores[0]->id => ['puntos' => 8],
            $this->jugadores[1]->id => ['puntos' => 4],
        ]);

        $response = $this->actingAs($this->user)->getJson("/user/liguillas/{$this->liguilla->id}/alineacion/{$jornada->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status'       => 'ok',
                'total_puntos' => 12,
            ])
            ->assertJsonCount(2, 'jugadores')
            ->assertJsonFragment([
                'id'     => $this->jugadores[0]->id,
                'nombre' => $this->jugadores[0]->nombre,
                'puntos' => 8,
            ])
            ->assertJsonFragment([
                'id'     => $this->jugadores[1]->id,
                'nombre' => $this->jugadores[1]->nombre,
                'puntos' => 4,
            ]);
    }
}
