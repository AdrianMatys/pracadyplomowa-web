<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Course::withCount('lessons')->with('tags')->orderBy('id')->get());
    }

    public function getImages(): JsonResponse
    {
        $files = glob(public_path('images/courses/*'));
        $images = array_map(function ($file) {
            return '/images/courses/'.basename($file);
        }, $files);

        return response()->json($images);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'reward' => 'nullable|integer|min:0',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'existing_image_path' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/courses'), $filename);
            $validated['image_path'] = 'images/courses/'.$filename;
        } elseif ($request->filled('existing_image_path')) {
            $validated['image_path'] = $request->input('existing_image_path');
        }

        $course = Course::create($validated);

        if (isset($validated['tags'])) {
            $course->tags()->sync($validated['tags']);
        }

        \Illuminate\Support\Facades\Cache::flush();

        $course->loadCount('lessons');
        $course->load('tags');

        $this->logAction('admin_course_create', "Admin utworzył kurs '{$course->title}' (ID: {$course->id})", ['course_id' => $course->id]);

        return response()->json($course, 201);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json($course->load(['lessons.exercises', 'tags'])->loadCount('lessons'));
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'reward' => 'nullable|integer|min:0',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'existing_image_path' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($course->image_path && file_exists(public_path($course->image_path))) {
                unlink(public_path($course->image_path));
            }

            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/courses'), $filename);
            $validated['image_path'] = 'images/courses/'.$filename;
        } elseif ($request->filled('existing_image_path')) {
            $validated['image_path'] = $request->input('existing_image_path');
        }

        $oldTitle = $course->title;
        $oldReward = $course->reward ?? 0;
        $course->update($validated);
        $newReward = $course->reward ?? 0;

        if ($oldReward !== $newReward) {
            $diff = $newReward - $oldReward;
            $userIds = \App\Models\Progress::where('course_id', $course->id)->where('status', 'completed')->pluck('user_id');

            if ($userIds->isNotEmpty()) {
                \App\Models\Profile::whereIn('user_id', $userIds)->increment('experience_points', $diff);
                $this->logAction('admin_xp_adjustment', "Zaktualizowano PD dla {$userIds->count()} użytkowników o {$diff} pkt", ['course_id' => $course->id, 'xp_diff' => $diff, 'affected_users' => $userIds->count()]);
            }
        }

        if (isset($validated['tags'])) {
            $course->tags()->sync($validated['tags']);
        }

        \Illuminate\Support\Facades\Cache::flush();

        $this->logAction('admin_course_update', "Admin zaktualizował kurs '{$course->title}' (ID: {$course->id})", ['course_id' => $course->id, 'old_title' => $oldTitle]);

        return response()->json($course);
    }

    public function reorderLessons(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:lessons,id',
            'lessons.*.order' => 'required|integer',
        ]);

        foreach ($validated['lessons'] as $lessonData) {
            $course->lessons()->where('id', $lessonData['id'])->update(['order' => $lessonData['order']]);
        }

        $this->logAction('admin_lessons_reorder', "Admin zmienił kolejność lekcji w kursie '{$course->title}' (ID: {$course->id})", ['course_id' => $course->id]);

        return response()->json(['message' => 'Lessons reordered successfully']);
    }

    public function destroy(Course $course): JsonResponse
    {
        $courseTitle = $course->title;
        $courseId = $course->id;

        $course->delete();

        \Illuminate\Support\Facades\Cache::flush();

        $this->logAction('admin_course_delete', "Admin usunął kurs '{$courseTitle}' (ID: {$courseId})", ['course_id' => $courseId, 'course_title' => $courseTitle]);

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
