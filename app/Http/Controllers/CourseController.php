<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Progress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $lang = $request->header('Accept-Language', 'pl');
        $isEnglish = str_starts_with($lang, 'en');
        $cacheLang = $isEnglish ? 'en' : 'pl';

        $cacheKey = "courses_index_{$search}_{$cacheLang}";

        $translatedCourses = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(30), function () use ($search, $isEnglish) {
            $query = Course::query();

            if ($search) {
                $query->where('title', 'like', '%'.$search.'%');
            }

            $courses = $query->with('tags')
                ->withCount('lessons')
                ->withCount('progress')
                ->orderByRaw("CASE WHEN title = 'Darmowy kurs dla gości' THEN 0 ELSE 1 END")
                ->orderByRaw("CASE level WHEN 'junior' THEN 1 WHEN 'mid' THEN 2 WHEN 'senior' THEN 3 ELSE 4 END")
                ->get();

            return $courses->map(function ($course) use ($isEnglish) {
                $courseArray = $course->toArray();
                if ($isEnglish) {
                    $courseArray['title'] = $course->title_en ?: $course->title;
                    $courseArray['description'] = $course->description_en ?: $course->description;
                }

                return $courseArray;
            });
        });

        return response()->json($translatedCourses, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $lang = $request->header('Accept-Language', 'pl');
        $isEnglish = str_starts_with($lang, 'en');
        $cacheLang = $isEnglish ? 'en' : 'pl';
        $cacheKey = "course_show_{$id}_{$cacheLang}";

        $courseArray = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(30), function () use ($id, $isEnglish) {
            $course = Course::with(['lessons', 'lessons.exercises', 'tags'])->findOrFail($id);
            $courseArray = $course->toArray();

            if ($isEnglish) {
                $courseArray['title'] = $course->title_en ?: $course->title;
                $courseArray['description'] = $course->description_en ?: $course->description;
            }

            return $courseArray;
        });

        return response()->json($courseArray, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function enroll(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (Progress::where('user_id', $user->id)->where('course_id', $id)->exists()) {
            return response()->json(['message' => 'Already enrolled'], 200);
        }

        Progress::create([
            'user_id' => $user->id,
            'course_id' => $id,
            'status' => 'enrolled',
            'started_at' => now(),
        ]);

        $this->logAction('course_enroll', "Zapisano się na kurs ID: {$id}", ['course_id' => $id]);

        return response()->json(['message' => 'Enrolled successfully']);
    }

    public function unenroll(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $deleted = Progress::where('user_id', $user->id)->where('course_id', $id)->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Not enrolled in this course'], 404);
        }

        $this->logAction('course_unenroll', "Wypisano się z kursu ID: {$id}", ['course_id' => $id]);

        return response()->json(['message' => 'Successfully unenrolled']);
    }
}
