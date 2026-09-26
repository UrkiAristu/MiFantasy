<?php

namespace Database\Factories;

use App\Models\Torneo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Torneo>
 */
class TorneoFactory extends Factory
{
    protected $model = Torneo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inicio = fake()->dateTimeBetween('-2 months', 'now');
        $fin = (clone $inicio)->modify('+6 months');

        return [
            'nombre' => 'Liga ' . fake()->city() . ' ' . fake()->year(),
            'fecha_inicio' => $inicio->format('Y-m-d'),
            'fecha_fin' => $fin->format('Y-m-d'),
            'descripcion' => fake()->paragraph(),
            'logo' => '/assets/media/images/default-tournament.png',
            'estado' => 'activo',
            'modalidad' => 'sala',
            'usa_posiciones' => true,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activo',
        ]);
    }

    public function finalizado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'finalizado',
        ]);
    }

    public function f11(): static
    {
        return $this->state(fn (array $attributes) => [
            'modalidad' => '11',
        ]);
    }

    public function f7(): static
    {
        return $this->state(fn (array $attributes) => [
            'modalidad' => '7',
        ]);
    }

    public function sala(): static
    {
        return $this->state(fn (array $attributes) => [
            'modalidad' => 'sala',
        ]);
    }
}
