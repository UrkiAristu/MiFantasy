<?php

namespace Tests\Feature;

use App\Models\Liguilla;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivePointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_live_points_classification_for_authorized_user(): void
    {
        $user = User::factory()->create();
        $liguilla = Liguilla::factory()->create();
        $liguilla->usuarios()->attach($user, ['puntos' => 10]);

        $otherUser = User::factory()->create();
        $liguilla->usuarios()->attach($otherUser, ['puntos' => 20]);

        $response = $this->actingAs($user)
            ->getJson(route('liguillas.livePoints', $liguilla));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'clasificacion' => [
                    '*' => ['id', 'posicion', 'name', 'puntos'],
                ],
            ])
            ->assertJsonPath('clasificacion.0.name', $otherUser->name)
            ->assertJsonPath('clasificacion.0.puntos', 20)
            ->assertJsonPath('clasificacion.1.name', $user->name)
            ->assertJsonPath('clasificacion.1.puntos', 10);
    }

    public function test_denies_access_to_live_points_for_unauthorized_user(): void
    {
        $user = User::factory()->create();
        $liguilla = Liguilla::factory()->create();

        $response = $this->actingAs($user)
            ->getJson(route('liguillas.livePoints', $liguilla));

        $response->assertStatus(403);
    }
}
