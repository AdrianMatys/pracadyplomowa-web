<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(array $profileAttrs = []): User
    {
        $user = User::factory()->create();
        Profile::factory()->create(array_merge(['user_id' => $user->id], $profileAttrs));

        return $user;
    }

    // ── Get Profile ──────────────────────────────────────────────

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = $this->createAuthenticatedUser(['nickname' => 'TestUser']);

        $response = $this->actingAs($user)->getJson('/api/users/me');

        $response->assertOk()
            ->assertJsonPath('profile.nickname', 'TestUser');
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/users/me');

        $response->assertStatus(401);
    }

    // ── Update Profile ───────────────────────────────────────────

    public function test_user_can_update_nickname(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->putJson('/api/users/me', [
            'nickname' => 'NewNick',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.profile.nickname', 'NewNick');
    }

    public function test_user_can_update_bio(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->putJson('/api/users/me', [
            'bio' => 'I love coding!',
        ]);

        $response->assertOk();
        $this->assertEquals('I love coding!', $user->fresh()->profile->bio);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');
        $user = $this->createAuthenticatedUser();
        $file = UploadedFile::fake()->create('avatar.jpg', 200, 'image/jpeg');

        $response = $this->actingAs($user)->postJson('/api/users/me', [
            'avatar' => $file,
        ]);

        $response->assertOk();
        $this->assertStringContains('/storage/avatars/', $user->fresh()->profile->avatar_url);
    }

    public function test_user_can_update_email(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->putJson('/api/users/me', [
            'email' => 'newemail@example.com',
        ]);

        $response->assertOk();
        $this->assertEquals('newemail@example.com', $user->fresh()->email);
    }

    // ── Change Password ──────────────────────────────────────────

    public function test_user_can_change_password(): void
    {
        $user = $this->createAuthenticatedUser();
        $user->password = bcrypt('old-password');
        $user->save();

        $response = $this->actingAs($user)->putJson('/api/users/me/password', [
            'current_password' => 'old-password',
            'new_password' => 'new-password123',
            'new_password_confirmation' => 'new-password123',
        ]);

        $response->assertOk();
    }

    public function test_wrong_current_password_rejected(): void
    {
        $user = $this->createAuthenticatedUser();
        $user->password = bcrypt('correct');
        $user->save();

        $response = $this->actingAs($user)->putJson('/api/users/me/password', [
            'current_password' => 'wrong',
            'new_password' => 'new-password123',
            'new_password_confirmation' => 'new-password123',
        ]);

        $response->assertStatus(400);
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $user = $this->createAuthenticatedUser();
        $user->password = bcrypt('old-password');
        $user->save();

        $response = $this->actingAs($user)->putJson('/api/users/me/password', [
            'current_password' => 'old-password',
            'new_password' => 'new-password123',
            'new_password_confirmation' => 'different',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Custom assertion for string contains (PHPUnit 11+ compatibility).
     */
    private function assertStringContains(string $needle, ?string $haystack): void
    {
        $this->assertNotNull($haystack);
        $this->assertTrue(str_contains($haystack, $needle), "Expected '{$haystack}' to contain '{$needle}'");
    }
}
