<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->profile && $user->profile->streak > 0 && $user->profile->last_lesson_completed_at) {
            $lastDate = $user->profile->last_lesson_completed_at->copy()->startOfDay();
            $yesterday = now()->copy()->subDay()->startOfDay();
            if ($lastDate->lessThan($yesterday)) {
                $user->profile->streak = 0;
                $user->profile->save();
            }
        }

        return response()->json($user->load([
            'profile',
            'achievements',
            'progress.course' => function ($query) {
                $query->withCount('lessons')->with('lessons:id,course_id,title,order');
            },
        ]));
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'nickname' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|string|max:2048',
            'avatar' => 'nullable|image|max:2048',
            'theme' => 'nullable|in:light,dark',
            'email' => 'nullable|email|max:255|unique:users,email,'.$user->id,
            'email' => 'nullable|email|max:255|unique:users,email,'.$user->id,
        ]);

        if (isset($data['email'])) {
            $user->email = $data['email'];
            $user->save();
        }

        $profileData = [];
        foreach (['nickname', 'bio', 'avatar_url', 'theme'] as $field) {
            if ($request->has($field)) {
                $profileData[$field] = $data[$field];
            }
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $type = $file->getClientOriginalExtension();
            $data = file_get_contents($file->getRealPath());
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $profileData['avatar_url'] = $base64;
        }

        if (! empty($profileData)) {
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);
            $this->logAction('profile_update', 'Zaktualizowano profil', ['changes' => array_keys($profileData)]);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()->load('profile'),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Obecne hasło jest nieprawidłowe.'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Hasło zostało zmienione.']);
    }

    public function stats(Request $request): JsonResponse
    {
        return response()->json([
            'completed_courses' => 0,
            'days_learning' => 1,
            'points' => 0,
        ]);
    }
}
