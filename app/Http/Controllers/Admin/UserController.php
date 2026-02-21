<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('profile')->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($q) use ($search) {
                        $q->where('nickname', 'like', "%{$search}%");
                    });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function ban(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot ban yourself'], 403);
        }

        $user->is_banned = ! $user->is_banned;
        $user->save();

        $target = $user->profile->nickname ?? $user->email;
        $actionPl = $user->is_banned ? 'zablokował' : 'odblokował';
        $this->logAction('user_'.($user->is_banned ? 'banned' : 'unbanned'), "Admin {$actionPl} użytkownika [{$target}] (ID: {$user->id})", ['target_user_id' => $user->id, 'new_status' => $user->is_banned ? 'banned' : 'active']);

        return response()->json([
            'message' => $user->is_banned ? 'User banned successfully' : 'User unbanned successfully',
            'user' => $user->load('profile'),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete yourself'], 403);
        }

        $target = $user->profile->nickname ?? $user->email;
        $this->logAction('user_deleted', "Admin usunął użytkownika [{$target}] (ID: {$user->id})", ['target_user_id' => $user->id]);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function updateLevel(Request $request, User $user): JsonResponse
    {
        $request->validate(['level' => 'required|in:junior,mid,senior']);

        $user->level = $request->level;
        $user->save();

        $xp = 0;
        if ($request->level === 'mid') {
            $xp = 5000;
        } elseif ($request->level === 'senior') {
            $xp = 15000;
        }

        if (!$user->profile) {
            $user->profile()->create(['experience_points' => $xp]);
        } else {
            $user->profile->experience_points = $xp;
            $user->profile->save();
        }

        $this->enforceLevelRequirements($user);

        $target = $user->profile->nickname ?? $user->email;
        $this->logAction('user_level_update', "Admin zmienił poziom użytkownika [{$target}] (ID: {$user->id}) na {$request->level}. Punkty zsynchronizowane na {$xp}.", ['target_user_id' => $user->id, 'new_level' => $request->level, 'new_xp' => $xp]);

        return response()->json([
            'message' => 'User level updated successfully and synced with courses',
            'user' => $user->load('profile'),
        ]);
    }

    public function updateXP(Request $request, User $user): JsonResponse
    {
        $request->validate(['experience_points' => 'required|integer|min:0']);

        $xp = $request->experience_points;

        if (! $user->profile) {
            $user->profile()->create(['experience_points' => $xp]);
        } else {
            $user->profile->experience_points = $xp;
            $user->profile->save();
        }

        $levelNum = floor($xp / 1000) + 1;
        $newLevel = 'junior';

        if ($levelNum > 15) {
            $newLevel = 'senior';
        } elseif ($levelNum > 5) {
            $newLevel = 'mid';
        }

        if ($user->level !== $newLevel) {
            $user->level = $newLevel;
            $user->save();
            
            $this->enforceLevelRequirements($user);
        }

        $target = $user->profile->nickname ?? $user->email;
        $this->logAction('user_xp_update', "Admin zmienił PD użytkownika [{$target}] (ID: {$user->id}) na {$xp}. Nowy poziom: {$newLevel}", ['target_user_id' => $user->id, 'new_xp' => $xp, 'new_level' => $newLevel]);

        return response()->json([
            'message' => 'User XP and Level updated successfully',
            'user' => $user->load('profile'),
        ]);
    }

    private function enforceLevelRequirements(User $user): void
    {
        $userLevelNum = $this->levelToNumber($user->level);
        
        $enrolledProgress = \App\Models\Progress::where('user_id', $user->id)
            ->with('course')
            ->get();

        foreach ($enrolledProgress as $progress) {
            if (!$progress->course) continue;

            $courseLevelNum = $this->levelToNumber($progress->course->level);

            if ($courseLevelNum > $userLevelNum) {
                $progress->delete();
                $this->logAction('course_auto_unenroll', "Użytkownik (ID: {$user->id}) został automatycznie wypisany z kursu ID: {$progress->course_id} z powodu zbyt niskiego poziomu ({$user->level})", [
                    'user_id' => $user->id,
                    'course_id' => $progress->course_id,
                    'course_level' => $progress->course->level,
                    'user_level' => $user->level
                ]);
            }
        }
    }

    private function levelToNumber(string $level): int
    {
        return match (strtolower($level)) {
            'junior' => 1,
            'mid' => 2,
            'senior' => 3,
            default => 1,
        };
    }
}
