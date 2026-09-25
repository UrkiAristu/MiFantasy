<?php

namespace Database\Factories;

use App\Models\Alineacion;
use App\Models\Jornada;
use App\Models\Liguilla;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alineacion>
 */
class AlineacionFactory extends Factory
{
    protected $model = Alineacion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'liguilla_id' => Liguilla::factory(),
            'jornada_id' => null,
        ];
    }

    public function base(): static
    {
        return $this->state(fn (array $attributes) => [
            'jornada_id' => null,
        ]);
    }

    public function paraJornada(int|Jornada $jornada): static
    {
        return $this->state(fn (array $attributes) => [
            'jornada_id' => $jornada instanceof Jornada ? $jornada->id : $jornada,
        ]);
    }
}
