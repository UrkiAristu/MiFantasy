<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLocalDashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_local_dashboard_loads_successfully()
    {
        $admin = User::factory()->create([
            'admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin-local/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Panel de Control Local');
    }
}
