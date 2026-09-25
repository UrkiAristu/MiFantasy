<?php

namespace Database\Factories;

use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Partido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partido>
 */
class PartidoFactory extends Factory
{
    protected $model = Partido::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jornada_id' => Jornada::factory(),
            'equipo_local_id' => Equipo::factory(),
            'equipo_visitante_id' => Equipo::factory(),
            'fecha_partido' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'goles_local' => null,
            'goles_visitante' => null,
            'estado' => 'programado',
            'eventos' => null,
        ];
    }

    public function jugado(?int $golesLocal = null, ?int $golesVisitante = null): static
    {
        $gLocal = $golesLocal ?? fake()->numberBetween(0, 5);
        $gVisitante = $golesVisitante ?? fake()->numberBetween(0, 5);

        return $this->state(fn (array $attributes) => [
            'estado' => 'jugado',
            'goles_local' => $gLocal,
            'goles_visitante' => $gVisitante,
            'eventos' => [],
        ]);
    }

    public function programado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'programado',
            'goles_local' => null,
            'goles_visitante' => null,
            'eventos' => null,
        ]);
    }
}
