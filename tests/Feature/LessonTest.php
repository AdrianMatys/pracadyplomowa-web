<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): User
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_authenticated_user_can_view_lesson(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id, 'title' => 'First Lesson']);

        $response = $this->actingAs($user)->getJson("/api/courses/{$course->id}/lessons/{$lesson->id}");

        $response->assertOk()
            ->assertJsonFragment(['title' => 'First Lesson']);
    }

    public function test_unauthenticated_user_cannot_view_lesson(): void
    {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->getJson("/api/courses/{$course->id}/lessons/{$lesson->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_complete_lesson(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($user)
            ->postJson("/api/courses/{$course->id}/lessons/{$lesson->id}/complete");

        $response->assertOk()
            ->assertJsonStructure(['message', 'progress', 'course_completed'])
            ->assertJson(['message' => 'Lesson marked as complete']);
    }

    public function test_completing_lesson_creates_progress(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $this->actingAs($user)
            ->postJson("/api/courses/{$course->id}/lessons/{$lesson->id}/complete");

        $this->assertDatabaseHas('progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_completing_all_lessons_completes_course(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create(['reward' => 50]);
        $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'order' => 1]);
        $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'order' => 2]);

        $this->actingAs($user)
            ->postJson("/api/courses/{$course->id}/lessons/{$lesson1->id}/complete");

        $response = $this->actingAs($user)
            ->postJson("/api/courses/{$course->id}/lessons/{$lesson2->id}/complete");

        $response->assertOk()
            ->assertJson(['course_completed' => true]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\AchievementSeeder::class);
    }

    public function test_completing_lesson_updates_streak(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $this->actingAs($user)
            ->postJson("/api/courses/{$course->id}/lessons/{$lesson->id}/complete")
            ->assertOk();

        $user->refresh();
        $this->assertEquals(1, $user->profile->streak);
        $this->assertNotNull($user->profile->last_lesson_completed_at);
    }

    public function test_completing_lesson_saves_user_code(): void
    {
        $user = $this->createAuthenticatedUser();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $this->actingAs($user)
            ->postJson("/api/courses/{$course->id}/lessons/{$lesson->id}/complete", [
                'user_code' => 'console.log("Hello");',
            ]);

        $progress = $user->progress()->where('course_id', $course->id)->first();
        $this->assertNotNull($progress->saved_code);
        $this->assertEquals('console.log("Hello");', $progress->saved_code[$lesson->id]);
    }
}
