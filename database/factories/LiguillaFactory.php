<?php

namespace Database\Factories;

use App\Models\Liguilla;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Liguilla>
 */
class LiguillaFactory extends Factory
{
    protected $model = Liguilla::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Liguilla ' . fake()->company(),
            'torneo_id' => Torneo::factory(),
            'max_usuarios' => 10,
            'codigo_unico' => strtoupper(Str::random(8)),
            'creador_id' => User::factory(),
            'estado' => 'activa',
        ];
    }

    public function activa(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activa',
        ]);
    }

    public function cerrada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'cerrada',
        ]);
    }
}
