<?php

namespace Database\Factories;

use App\Models\Jugador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jugador>
 */
class JugadorFactory extends Factory
{
    protected $model = Jugador::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $posiciones = ['Portero', 'Defensa', 'Centrocampista', 'Delantero'];

        return [
            'nombre' => fake()->firstName('male'),
            'apellido1' => fake()->lastName(),
            'apellido2' => fake()->lastName(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-38 years', '-18 years')->format('Y-m-d'),
            'posicion' => fake()->randomElement($posiciones),
            'foto' => '/assets/media/images/default-player.png',
        ];
    }

    public function portero(): static
    {
        return $this->state(fn (array $attributes) => [
            'posicion' => 'Portero',
        ]);
    }

    public function defensa(): static
    {
        return $this->state(fn (array $attributes) => [
            'posicion' => 'Defensa',
        ]);
    }

    public function centrocampista(): static
    {
        return $this->state(fn (array $attributes) => [
            'posicion' => 'Centrocampista',
        ]);
    }

    public function delantero(): static
    {
        return $this->state(fn (array $attributes) => [
            'posicion' => 'Delantero',
        ]);
    }
}
