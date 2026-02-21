<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $lang = $request->header('Accept-Language', 'pl');
        $cacheKey = "dashboard_index_{$lang}";

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () {
            $recentNews = Article::latest()->take(3)->get();
            $recommendedCourses = Course::withCount('lessons')->with('tags')->take(3)->get();

            return [
                'news' => $recentNews,
                'recommended_courses' => $recommendedCourses,
                'user_stats' => [
                    'completed_lessons' => 0,
                    'total_hours' => 0,
                ],
            ];
        });

        return response()->json($data);
    }
}
