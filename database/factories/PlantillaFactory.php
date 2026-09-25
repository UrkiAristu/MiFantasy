<?php

namespace Database\Factories;

use App\Models\Liguilla;
use App\Models\Plantilla;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plantilla>
 */
class PlantillaFactory extends Factory
{
    protected $model = Plantilla::class;

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
        ];
    }
}
