<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LiguillaInscripcionTest extends TestCase
{
    use RefreshDatabase;

    private User $creador;

    private User $usuario;

    private Torneo $torneo;

    private Liguilla $liguilla;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $this->creador = User::factory()->create(['name' => 'Creador', 'email' => 'creador@test.com', 'active' => true]);
        $this->usuario = User::factory()->create(['name' => 'Usuario Test', 'email' => 'usuario@test.com', 'active' => true]);

        $this->torneo = new Torneo;
        $this->torneo->nombre = 'Torneo Inscripcion Test';
        $this->torneo->jugadores_por_equipo = 2;
        $this->torneo->fecha_inicio = now()->subDays(5);
        $this->torneo->fecha_fin = now()->addDays(20);
        $this->torneo->save();

        // Crear equipo y jugadores para plantilla aleatoria
        $equipo = new Equipo;
        $equipo->nombre = 'Equipo Test';
        $equipo->save();

        for ($i = 1; $i <= 6; $i++) {
            $jugador = new Jugador;
            $jugador->nombre = "Jugador $i";
            $jugador->apellido1 = "Apellido $i";
            $jugador->save();

            DB::table('equipo_jugador_torneo')->insert([
                'jugador_id' => $jugador->id,
                'equipo_id' => $equipo->id,
                'torneo_id' => $this->torneo->id,
                'goles' => 0,
                'asistencias' => 0,
                'puntos' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->liguilla = new Liguilla;
        $this->liguilla->nombre = 'Liguilla Cupos';
        $this->liguilla->torneo_id = $this->torneo->id;
        $this->liguilla->max_usuarios = 2; // Máximo 2 usuarios
        $this->liguilla->codigo_unico = 'LIGABC12';
        $this->liguilla->creador_id = $this->creador->id;
        $this->liguilla->save();

        // Inscribir al creador (1 de 2 plazas ocupadas)
        $this->liguilla->usuarios()->attach($this->creador->id);
    }

    public function test_usuario_puede_unirse_a_liguilla_con_codigo_valido(): void
    {
        $response = $this->actingAs($this->usuario)->post('/user/liguillas/unirse', [
            'codigo' => 'ligabc12', // minúsculas para validar normalización a mayúsculas
        ]);

        $response->assertRedirect('/user/liguillas')
            ->assertSessionHas('success', 'Te has unido correctamente a la liguilla.');

        // Verificar que el usuario está en la liguilla
        $this->assertTrue($this->liguilla->usuarios()->where('user_id', $this->usuario->id)->exists());

        // Verificar que se le creó plantilla
        $plantilla = Plantilla::where('liguilla_id', $this->liguilla->id)
            ->where('user_id', $this->usuario->id)
            ->first();

        $this->assertNotNull($plantilla);
        $this->assertGreaterThan(0, $plantilla->jugadores()->count());
    }

    public function test_rechaza_codigo_inexistente(): void
    {
        $response = $this->actingAs($this->usuario)->post('/user/liguillas/unirse', [
            'codigo' => 'INVAL123',
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['codigo' => 'Código de liguilla no válido.']);

        $this->assertFalse($this->liguilla->usuarios()->where('user_id', $this->usuario->id)->exists());
    }

    public function test_rechaza_si_usuario_ya_esta_inscrito(): void
    {
        // El creador ya está inscrito
        $response = $this->actingAs($this->creador)->post('/user/liguillas/unirse', [
            'codigo' => 'LIGABC12',
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['codigo' => 'Ya estás inscrito en esta liguilla.']);
    }

    public function test_rechaza_inscripcion_cuando_liguilla_esta_llena(): void
    {
        // Llenar el cupo restante (1 plaza libre) con otro usuario
        $otroUsuario = User::factory()->create(['name' => 'Otro Usuario', 'email' => 'otro@test.com', 'active' => true]);
        $this->liguilla->usuarios()->attach($otroUsuario->id);

        $this->assertEquals(2, $this->liguilla->usuarios()->count());
        $this->assertEquals(2, $this->liguilla->max_usuarios);

        // Intentar unir a $this->usuario cuando no hay cupo
        $response = $this->actingAs($this->usuario)->post('/user/liguillas/unirse', [
            'codigo' => 'LIGABC12',
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['codigo' => 'La liguilla ya está completa.']);

        $this->assertFalse($this->liguilla->usuarios()->where('user_id', $this->usuario->id)->exists());
    }

    public function test_validacion_formato_codigo(): void
    {
        $response = $this->actingAs($this->usuario)->post('/user/liguillas/unirse', [
            'codigo' => 'CORTO',
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('codigo');
    }
}
