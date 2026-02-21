<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Request $request, string $courseId, string $lessonId): JsonResponse
    {
        $lesson = Lesson::where('course_id', $courseId)
            ->where('id', $lessonId)
            ->with('exercises')
            ->firstOrFail();

        $user = $request->user('sanctum');
        if (! $user && $lesson->order > 0) {
             return response()->json(['message' => 'Unauthorized. This lesson requires enrollment.'], 403);
        }

        return response()->json($lesson);
    }

    public function complete(Request $request, string $courseId, string $lessonId): JsonResponse
    {
        $user = $request->user();
        $progress = $user->progress()->firstOrCreate(
            ['course_id' => $courseId],
            ['status' => 'started', 'started_at' => now()]
        );

        $completedIds = $progress->completed_lesson_ids ?? [];
        if (! in_array($lessonId, $completedIds)) {
            $completedIds[] = $lessonId;
            $progress->completed_lesson_ids = $completedIds;
            $this->logAction('lesson_complete', "Ukończono lekcję ID: {$lessonId} w kursie ID: {$courseId}", ['course_id' => $courseId, 'lesson_id' => $lessonId]);
        }

        if ($user->profile) {
            $user->profile->updateStreak();
        }

        if ($request->has('user_code')) {
            $savedCode = $progress->saved_code ?? [];
            $savedCode[$lessonId] = $request->input('user_code');
            $progress->saved_code = $savedCode;
        }

        $progress->save();

        $this->checkAchievements($user, $progress);
        $this->checkCourseCompletion($user, $progress, $courseId, $completedIds);

        return response()->json([
            'message' => 'Lesson marked as complete',
            'progress' => $progress,
            'course_completed' => $progress->status === 'completed',
        ]);
    }

    private function checkAchievements($user, $progress): void
    {
        $allCompleted = $user->progress()->whereNotNull('completed_lesson_ids')->get()->flatMap(fn ($p) => $p->completed_lesson_ids ?? []);

        if ($allCompleted->count() <= 1) {
            if ($user->unlockAchievement('first_steps')) {
                $this->logAction('achievement_unlocked', 'Odblokowano osiągnięcie: Pierwsze kroki', ['achievement_id' => 'first_steps']);
            }
        }

        $hour = (int) now()->format('H');
        if ($hour >= 22 || $hour < 4) {
            if ($user->unlockAchievement('night_owl')) {
                $this->logAction('achievement_unlocked', 'Odblokowano osiągnięcie: Nocny marek', ['achievement_id' => 'night_owl']);
            }
        }

        if ($allCompleted->unique()->count() >= 50) {
            if ($user->unlockAchievement('code_master')) {
                $this->logAction('achievement_unlocked', 'Odblokowano osiągnięcie: Mistrz kodu', ['achievement_id' => 'code_master']);
            }
        }
    }

    private function checkCourseCompletion($user, $progress, $courseId, $completedIds): void
    {
        $course = Course::with('lessons')->findOrFail($courseId);
        $totalLessons = $course->lessons->count();

        if (count($completedIds) >= $totalLessons && $progress->status !== 'completed') {
            $progress->status = 'completed';
            $progress->completed_at = now();
            $progress->save();

            $reward = $course->reward ?? 0;
            if ($reward > 0 && $user->profile) {
                $user->profile->increment('experience_points', $reward);
                $this->logAction('xp_reward_course', "Zdobyto {$reward} PD za ukończenie kursu ID: {$courseId}", ['course_id' => $courseId, 'reward' => $reward]);
            }

            $startedAt = $progress->started_at ?? $progress->created_at;
            if ($startedAt && now()->diffInDays($startedAt) <= 7) {
                if ($user->unlockAchievement('fast_learner')) {
                    $this->logAction('achievement_unlocked', 'Odblokowano osiągnięcie: Szybki uczeń', ['achievement_id' => 'fast_learner']);
                }
            }

            $this->logAction('course_complete', "Ukończono kurs ID: {$courseId}", ['course_id' => $courseId]);
        }
    }
}
