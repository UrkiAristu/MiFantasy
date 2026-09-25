<?php

namespace Database\Factories;

use App\Models\Estadistica;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estadistica>
 */
class EstadisticaFactory extends Factory
{
    protected $model = Estadistica::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $minutos = fake()->numberBetween(15, 90);
        $goles = fake()->randomElement([0, 0, 0, 1, 1, 2]);
        $asistencias = fake()->randomElement([0, 0, 1, 1, 2]);
        $amarillas = fake()->randomElement([0, 0, 0, 1]);
        $rojas = fake()->randomElement([0, 0, 0, 0, 0, 1]);
        $faltas = fake()->numberBetween(0, 4);
        $paradas = fake()->numberBetween(0, 5);
        $resultado = fake()->randomElement(['ganado', 'empatado', 'perdido']);

        $puntosResultado = match ($resultado) {
            'ganado' => 3,
            'empatado' => 1,
            default => 0,
        };

        $puntos = $puntosResultado
            + ($goles * 5)
            + ($asistencias * 3)
            + ($paradas * 2)
            - ($amarillas * 3)
            - ($rojas * 5)
            - ($faltas * 1);

        return [
            'jugador_id' => Jugador::factory(),
            'partido_id' => Partido::factory(),
            'posicion' => fake()->randomElement(['Portero', 'Defensa', 'Centrocampista', 'Delantero']),
            'minutos' => $minutos,
            'goles' => $goles,
            'asistencias' => $asistencias,
            'tarjetas_amarillas' => $amarillas,
            'tarjetas_rojas' => $rojas,
            'faltas' => $faltas,
            'paradas' => $paradas,
            'resultado' => $resultado,
            'puntos' => $puntos,
        ];
    }
}
