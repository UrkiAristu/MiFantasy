<?php

namespace Database\Factories;

use App\Models\Jornada;
use App\Models\Torneo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jornada>
 */
class JornadaFactory extends Factory
{
    protected $model = Jornada::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orden = fake()->numberBetween(1, 38);
        $fechaInicio = fake()->dateTimeBetween('-1 month', '+2 months');
        $fechaFin = (clone $fechaInicio)->modify('+3 days');
        $cierre = (clone $fechaInicio)->modify('-2 hours');

        return [
            'torneo_id' => Torneo::factory(),
            'nombre' => "Jornada {$orden}",
            'orden' => $orden,
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'fecha_cierre_alineaciones' => $cierre->format('Y-m-d H:i:s'),
            'alineaciones_congeladas' => false,
        ];
    }

    public function congelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'alineaciones_congeladas' => true,
        ]);
    }

    public function abierta(): static
    {
        return $this->state(fn (array $attributes) => [
            'alineaciones_congeladas' => false,
        ]);
    }
}
