<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function createUserWithProfile(array $userAttrs = [], array $profileAttrs = []): User
    {
        $user = User::factory()->create($userAttrs);
        Profile::factory()->create(array_merge(['user_id' => $user->id], $profileAttrs));

        return $user;
    }

    // ── Registration ─────────────────────────────────────────────

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'password' => 'password123',
            'recaptcha_token' => 'test-token',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user']);

        $this->assertDatabaseHas('users', ['email' => 'jan@example.com']);
        $this->assertDatabaseHas('profiles', ['nickname' => 'Jan Kowalski']);
    }

    public function test_register_requires_all_fields(): void
    {
        // No mock needed as validation fails before captcha (if fields missing)
        // But code validates captcha token presence first?
        // 'recaptcha_token' => 'required' in validation.
        // If token provided, it validates. Here we miss required fields.

        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'recaptcha_token']);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        // Validation for unique email happens BEFORE captcha check?
        // Request validation runs all rules. Unique check is DB query. Recaptcha rule is custom.
        // If standard validation fails, it throws ValidationException.
        // Recaptcha is verified manually AFTER $request->validate().
        // So we don't need mock if validation fails!
        
        $this->createUserWithProfile(['email' => 'taken@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Another User',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'recaptcha_token' => 'test-token',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_rejects_short_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => '123',
            'recaptcha_token' => 'test-token',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // ── Login ────────────────────────────────────────────────────

    public function test_user_can_login_with_valid_credentials(): void
    {
        $this->createUserWithProfile(['email' => 'user@example.com', 'password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'secret123',
            'recaptcha_token' => 'test-token',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'user']);
    }

    public function test_login_rejects_wrong_password(): void
    {
        $this->createUserWithProfile(['email' => 'user@example.com', 'password' => bcrypt('correct')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrong',
            'recaptcha_token' => 'test-token',
        ]);

        // Auth::attempt fails. Captcha MUST PASS FIRST because verifyRecaptcha is called BEFORE Auth::attempt.
        // See AuthController lines 82 vs 88.
        // So we need mock.

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_banned_user_cannot_login(): void
    {
        $this->createUserWithProfile([
            'email' => 'banned@example.com',
            'password' => bcrypt('password'),
            'is_banned' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'banned@example.com',
            'password' => 'password',
            'recaptcha_token' => 'test-token',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ── Logout ───────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->createUserWithProfile();

        $response = $this->actingAs($user)
            ->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJson(['message' => 'Logged out successfully']);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
}
