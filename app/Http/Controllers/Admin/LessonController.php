<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'has_exercise' => 'nullable|boolean',
            'exercise_title' => 'nullable|string|max:255',
            'exercise_description' => 'nullable|string',
            'initial_code' => 'nullable|string',
            'expected_output' => 'nullable|string',
            'judge0_language_id' => 'nullable|integer',
            'validation_regex' => 'nullable|string',
            'test_code' => 'nullable|string',
            'hint' => 'nullable|string',
            'hint_2' => 'nullable|string',
            'preview_type' => 'nullable|string',
        ]);

        $validated['order'] = ($course->lessons()->max('order') ?? 0) + 1;
        $lesson = $course->lessons()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'order' => $validated['order'],
        ]);

        if (!empty($validated['has_exercise'])) {
            $lesson->exercises()->create([
                'title' => $validated['exercise_title'] ?? $validated['title'],
                'description' => $validated['exercise_description'] ?? $validated['description'] ?? '',
                'initial_code' => $validated['initial_code'] ?? '',
                'expected_output' => $validated['expected_output'] ?? null,
                'judge0_language_id' => $validated['judge0_language_id'] ?? null,
                'validation_regex' => $validated['validation_regex'] ?? null,
                'test_code' => $validated['test_code'] ?? null,
                'hint' => $validated['hint'] ?? '',
                'hint_2' => $validated['hint_2'] ?? null,
                'preview_type' => $validated['preview_type'] ?? 'none',
            ]);
        }

        $lesson->load('exercises');

        $this->logAction('admin_lesson_create', "Admin utworzył lekcję '{$lesson->title}' (ID: {$lesson->id}) w kursie '{$course->title}' (ID: {$course->id})", ['lesson_id' => $lesson->id, 'course_id' => $course->id]);

        return response()->json($lesson, 201);
    }

    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'has_exercise' => 'nullable|boolean',
            'exercise_title' => 'nullable|string|max:255',
            'exercise_description' => 'nullable|string',
            'initial_code' => 'nullable|string',
            'expected_output' => 'nullable|string',
            'judge0_language_id' => 'nullable|integer',
            'validation_regex' => 'nullable|string',
            'test_code' => 'nullable|string',
            'hint' => 'nullable|string',
            'hint_2' => 'nullable|string',
            'preview_type' => 'nullable|string',
        ]);

        $oldTitle = $lesson->title;
        $lesson->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
        ]);

        if (!empty($validated['has_exercise'])) {
            $exerciseData = [
                'title' => $validated['exercise_title'] ?? $validated['title'],
                'description' => $validated['exercise_description'] ?? $validated['description'] ?? '',
                'initial_code' => $validated['initial_code'] ?? '',
                'expected_output' => $validated['expected_output'] ?? null,
                'judge0_language_id' => $validated['judge0_language_id'] ?? null,
                'validation_regex' => $validated['validation_regex'] ?? null,
                'test_code' => $validated['test_code'] ?? null,
                'hint' => $validated['hint'] ?? '',
                'hint_2' => $validated['hint_2'] ?? null,
                'preview_type' => $validated['preview_type'] ?? 'none',
            ];

            $exercise = $lesson->exercises()->first();
            if ($exercise) {
                $exercise->update($exerciseData);
            } else {
                $lesson->exercises()->create($exerciseData);
            }
        } else {
            $lesson->exercises()->delete();
        }

        $lesson->load('exercises');

        $this->logAction('admin_lesson_update', "Admin zaktualizował lekcję '{$lesson->title}' (ID: {$lesson->id})", ['lesson_id' => $lesson->id, 'old_title' => $oldTitle]);

        return response()->json($lesson);
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        $data = ['lesson_id' => $lesson->id, 'course_id' => $lesson->course_id, 'lesson_title' => $lesson->title];
        $desc = "Admin usunął lekcję '{$lesson->title}' (ID: {$lesson->id}) z kursu ID: {$lesson->course_id}";

        $lesson->delete();

        $this->logAction('admin_lesson_delete', $desc, $data);

        return response()->json(['message' => 'Lesson deleted']);
    }
}
