<?php

namespace Database\Factories;

use App\Models\Equipo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipo>
 */
class EquipoFactory extends Factory
{
    protected $model = Equipo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombres = [
            'Real Madrid', 'FC Barcelona', 'Atlético de Madrid', 'Sevilla FC', 'Real Sociedad',
            'Real Betis', 'Villarreal CF', 'Athletic Club', 'Valencia CF', 'CA Osasuna',
            'Celta de Vigo', 'RCD Mallorca', 'Girona FC', 'Rayo Vallecano', 'Getafe CF',
            'UD Las Palmas', 'Deportivo Alavés', 'Granada CF', 'Cádiz CF', 'UD Almería'
        ];

        return [
            'nombre' => fake()->unique()->randomElement($nombres) . ' ' . fake()->unique()->numberBetween(1, 9999),
            'logo' => '/assets/media/images/default-team.png',
        ];
    }
}
