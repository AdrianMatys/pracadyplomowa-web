<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\Progress;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): User
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    // ── Public Endpoints ─────────────────────────────────────────

    public function test_can_list_courses(): void
    {
        Course::factory()->count(3)->create();

        $response = $this->getJson('/api/courses');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function test_courses_include_lesson_count(): void
    {
        $course = Course::factory()->create();
        Lesson::factory()->count(5)->create(['course_id' => $course->id]);

        $response = $this->getJson('/api/courses');

        $response->assertOk();
        $this->assertEquals(5, $response->json('0.lessons_count'));
    }

    public function test_can_show_single_course(): void
    {
        $course = Course::factory()->create(['title' => 'Laravel Basics']);

        $response = $this->getJson("/api/courses/{$course->id}");

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Laravel Basics']);
    }

    public function test_show_nonexistent_course_returns_404(): void
    {
        $response = $this->getJson('/api/courses/999');

        $response->assertStatus(404);
    }

    public function test_courses_include_tags(): void
    {
        $course = Course::factory()->create();
        $tag = Tag::factory()->create(['name' => 'PHP']);
        $course->tags()->attach($tag);

        $response = $this->getJson('/api/courses');

        $response->assertOk()
            ->assertJsonFragment(['name' => 'PHP']);
    }

    // ── Enroll / Unenroll (auth required) ────────────────────────

    public function test_authenticated_user_can_enroll_in_course(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/courses/{$course->id}/enroll");

        $response->assertOk()
            ->assertJson(['message' => 'Enrolled successfully']);

        $this->assertDatabaseHas('progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);
    }

    public function test_cannot_enroll_twice(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();
        Progress::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson("/api/courses/{$course->id}/enroll");

        $response->assertOk()
            ->assertJson(['message' => 'Already enrolled']);
    }

    public function test_authenticated_user_can_unenroll(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();
        Progress::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
            'started_at' => now(),
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/courses/{$course->id}/enroll");

        $response->assertOk()
            ->assertJson(['message' => 'Successfully unenrolled']);

        $this->assertDatabaseMissing('progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_unenroll_when_not_enrolled_returns_404(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/courses/{$course->id}/enroll");

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_enroll(): void
    {
        $course = Course::factory()->create();

        $response = $this->postJson("/api/courses/{$course->id}/enroll");

        $response->assertStatus(401);
    }
}
