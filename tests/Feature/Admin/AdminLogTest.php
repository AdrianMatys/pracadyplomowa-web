<?php

namespace Tests\Feature\Admin;

use App\Models\Log;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLogTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->admin()->create(['password' => bcrypt('admin-pass')]);
        Profile::factory()->create(['user_id' => $admin->id]);

        return $admin;
    }

    // ── Authorization ────────────────────────────────────────────

    public function test_non_admin_cannot_access_logs(): void
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/admin/logs');

        $response->assertStatus(403);
    }

    // ── List ─────────────────────────────────────────────────────

    public function test_admin_can_list_logs(): void
    {
        $admin = $this->createAdmin();
        Log::create([
            'user_id' => $admin->id,
            'user_name' => 'Admin',
            'action' => 'test',
            'description' => 'Test log entry',
        ]);

        $response = $this->actingAs($admin)->getJson('/api/admin/logs');

        $response->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_admin_can_search_logs(): void
    {
        $admin = $this->createAdmin();
        Log::create([
            'user_id' => $admin->id,
            'user_name' => 'Admin',
            'action' => 'login',
            'description' => 'Unique search term xyz123',
        ]);

        $response = $this->actingAs($admin)->getJson('/api/admin/logs?search=xyz123');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('total'));
    }

    // ── Clear ────────────────────────────────────────────────────

    public function test_admin_can_clear_logs_with_password(): void
    {
        $admin = $this->createAdmin();
        Log::create([
            'user_id' => $admin->id,
            'user_name' => 'Admin',
            'action' => 'test',
            'description' => 'Will be cleared',
        ]);

        $response = $this->actingAs($admin)->deleteJson('/api/admin/logs/clear', [
            'password' => 'admin-pass',
        ]);

        $response->assertOk();
    }

    public function test_clear_logs_requires_correct_password(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->deleteJson('/api/admin/logs/clear', [
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }

    public function test_clear_logs_requires_password_field(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->deleteJson('/api/admin/logs/clear', []);

        $response->assertStatus(422);
    }
}
