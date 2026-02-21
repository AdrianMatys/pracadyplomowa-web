<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCourseTest extends TestCase
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

    public function test_non_admin_cannot_access_admin_courses(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->getJson('/api/admin/courses');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_admin_courses(): void
    {
        $response = $this->getJson('/api/admin/courses');

        $response->assertStatus(401);
    }

    // ── List ─────────────────────────────────────────────────────

    public function test_admin_can_list_courses(): void
    {
        $admin = $this->createAdmin();
        Course::factory()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/admin/courses');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    // ── Create ───────────────────────────────────────────────────

    public function test_admin_can_create_course(): void
    {
        $admin = $this->createAdmin();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($admin)->postJson('/api/admin/courses', [
            'title' => 'New Course',
            'description' => 'Course description',
            'duration' => 120,
            'reward' => 50,
            'tags' => [$tag->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'New Course']);

        $this->assertDatabaseHas('courses', ['title' => 'New Course']);
    }

    public function test_create_course_requires_title(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->postJson('/api/admin/courses', [
            'description' => 'No title',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    // ── Show ─────────────────────────────────────────────────────

    public function test_admin_can_show_course(): void
    {
        $admin = $this->createAdmin();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->getJson("/api/admin/courses/{$course->id}");

        $response->assertOk()
            ->assertJsonFragment(['title' => $course->title]);
    }

    // ── Update ───────────────────────────────────────────────────

    public function test_admin_can_update_course(): void
    {
        $admin = $this->createAdmin();
        $course = Course::factory()->create(['title' => 'Old Title']);

        $response = $this->actingAs($admin)->putJson("/api/admin/courses/{$course->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated desc',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Updated Title']);
    }

    // ── Delete ───────────────────────────────────────────────────

    public function test_admin_can_delete_course(): void
    {
        $admin = $this->createAdmin();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/courses/{$course->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    // ── Reorder Lessons ──────────────────────────────────────────

    public function test_admin_can_reorder_lessons(): void
    {
        $admin = $this->createAdmin();
        $course = Course::factory()->create();
        $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'order' => 2]);

        $response = $this->actingAs($admin)->postJson("/api/admin/courses/{$course->id}/reorder", [
            'lessons' => [
                ['id' => $lesson1->id, 'order' => 2],
                ['id' => $lesson2->id, 'order' => 1],
            ],
        ]);

        $response->assertOk();
        $this->assertEquals(2, $lesson1->fresh()->order);
        $this->assertEquals(1, $lesson2->fresh()->order);
    }
}
