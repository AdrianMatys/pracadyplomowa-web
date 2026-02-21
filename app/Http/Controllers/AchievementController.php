<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(): JsonResponse
    {
        $achievements = [
            ['id' => 1, 'name' => 'First Steps', 'description' => 'Completed your first lesson.', 'icon' => 'star'],
            ['id' => 2, 'name' => 'Code Master', 'description' => 'Solved 10 exercises.', 'icon' => 'trophy'],
            ['id' => 3, 'name' => 'Night Owl', 'description' => 'Logged in after midnight.', 'icon' => 'moon'],
        ];

        return response()->json($achievements);
    }

    public function userAchievements(Request $request): JsonResponse
    {
        $allAchievements = Achievement::all();

        $earnedIds = $request->user()->achievements()->pluck('achievements.id')->toArray();

        $merged = $allAchievements->map(function ($achievement) use ($earnedIds) {
            $data = $achievement->toArray();
            $data['isEarned'] = in_array($achievement->id, $earnedIds);

            return $data;
        });

        return response()->json($merged);
    }
}
