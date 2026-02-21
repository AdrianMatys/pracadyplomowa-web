<?php

namespace Tests\Feature\Admin;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->admin()->create();
        Profile::factory()->create(['user_id' => $admin->id]);

        return $admin;
    }

    private function createRegularUser(): User
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    // ── Authorization ────────────────────────────────────────────

    public function test_non_admin_cannot_list_users(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->getJson('/api/admin/users');

        $response->assertStatus(403);
    }

    // ── List ─────────────────────────────────────────────────────

    public function test_admin_can_list_users(): void
    {
        $admin = $this->createAdmin();
        $this->createRegularUser();
        $this->createRegularUser();

        $response = $this->actingAs($admin)->getJson('/api/admin/users');

        $response->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_admin_can_search_users(): void
    {
        $admin = $this->createAdmin();
        User::factory()->create(['email' => 'searchme@example.com']);

        $response = $this->actingAs($admin)->getJson('/api/admin/users?search=searchme');

        $response->assertOk();
    }

    // ── Ban / Unban ──────────────────────────────────────────────

    public function test_admin_can_ban_user(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser();

        $response = $this->actingAs($admin)->patchJson("/api/admin/users/{$user->id}/ban");

        $response->assertOk();
        $this->assertTrue($user->fresh()->is_banned);
    }

    public function test_admin_can_unban_user(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->banned()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->patchJson("/api/admin/users/{$user->id}/ban");

        $response->assertOk();
        $this->assertFalse($user->fresh()->is_banned);
    }

    public function test_admin_cannot_ban_self(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->patchJson("/api/admin/users/{$admin->id}/ban");

        $response->assertStatus(403);
    }

    // ── Delete ───────────────────────────────────────────────────

    public function test_admin_can_delete_user(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/users/{$user->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/users/{$admin->id}");

        $response->assertStatus(403);
    }

    // ── Update Level ─────────────────────────────────────────────

    public function test_admin_can_update_user_level(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser();

        $response = $this->actingAs($admin)->patchJson("/api/admin/users/{$user->id}/level", [
            'level' => 'senior',
        ]);

        $response->assertOk();
        $this->assertEquals('senior', $user->fresh()->level);
    }

    public function test_level_must_be_valid_value(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser();

        $response = $this->actingAs($admin)->patchJson("/api/admin/users/{$user->id}/level", [
            'level' => 'invalid',
        ]);

        $response->assertStatus(422);
    }

    // ── Update XP ────────────────────────────────────────────────

    public function test_admin_can_update_user_xp(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser();

        $response = $this->actingAs($admin)->patchJson("/api/admin/users/{$user->id}/xp", [
            'experience_points' => 500,
        ]);

        $response->assertOk();
        $this->assertEquals(500, $user->fresh()->profile->experience_points);
    }

    public function test_admin_update_xp_updates_level_automatically(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createRegularUser();
        $user->update(['level' => 'junior']);
        $user->profile()->update(['experience_points' => 0]);

        // Test upgrade to Mid (6000 XP)
        $this->actingAs($admin)
            ->patchJson("/api/admin/users/{$user->id}/xp", [
                'experience_points' => 6000,
            ])
            ->assertOk()
            ->assertJsonPath('user.level', 'mid');

        $this->assertEquals('mid', $user->fresh()->level);

        // Test upgrade to Senior (16000 XP)
        $this->actingAs($admin)
            ->patchJson("/api/admin/users/{$user->id}/xp", [
                'experience_points' => 16000,
            ])
            ->assertOk()
            ->assertJsonPath('user.level', 'senior');

        $this->assertEquals('senior', $user->fresh()->level);

        // Test downgrade to Junior (500 XP)
        $this->actingAs($admin)
            ->patchJson("/api/admin/users/{$user->id}/xp", [
                'experience_points' => 500,
            ])
            ->assertOk()
            ->assertJsonPath('user.level', 'junior');

        $this->assertEquals('junior', $user->fresh()->level);
    }
}
