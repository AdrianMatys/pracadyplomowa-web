<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLink']);
Route::post('/auth/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword']);
Route::get('/auth/reset-token/{token}', [\App\Http\Controllers\PasswordResetController::class, 'getEmailByToken']);

Route::get('/auth/verify-email/{id}/{hash}', [\App\Http\Controllers\EmailVerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/auth/resend-verification', [\App\Http\Controllers\EmailVerificationController::class, 'resend'])
    ->name('verification.send');

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::get('/technologies', [\App\Http\Controllers\TagController::class, 'index']);
Route::get('/achievements', [AchievementController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/courses/{courseId}/lessons/{lessonId}', [LessonController::class, 'show']);
Route::post('/submissions', [SubmissionController::class, 'submit']);
Route::get('/submissions/{token}', [SubmissionController::class, 'show']);

Route::middleware(['auth:sanctum', 'check_status'])->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh-token', [AuthController::class, 'refresh']);

    Route::get('/users/me', [UserController::class, 'me']);
    Route::put('/users/me', [UserController::class, 'update']);
    Route::post('/users/me', [UserController::class, 'update']);
    Route::put('/users/me/password', [UserController::class, 'updatePassword']);
    Route::get('/users/me/stats', [UserController::class, 'stats']);
    Route::get('/users/me/achievements', [AchievementController::class, 'userAchievements']);

    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
    Route::delete('/courses/{id}/enroll', [CourseController::class, 'unenroll']);
    Route::get('/courses/{id}/progress', function (string $id): JsonResponse {
        return response()->json(['completed_lessons' => []]);
    });

    Route::post('/courses/{courseId}/lessons/{lessonId}/complete', [LessonController::class, 'complete']);
    Route::post('/exercises/{id}/submit', [SubmissionController::class, 'storeExerciseSubmission']);

    Route::prefix('admin')->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class)->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::patch('/users/{user}/ban', [\App\Http\Controllers\Admin\UserController::class, 'ban']);
        Route::patch('/users/{user}/level', [\App\Http\Controllers\Admin\UserController::class, 'updateLevel']);
        Route::patch('/users/{user}/xp', [\App\Http\Controllers\Admin\UserController::class, 'updateXP']);
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);
        Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class, 'index']);
        Route::delete('/logs/clear', [\App\Http\Controllers\Admin\LogController::class, 'clear']);

        Route::get('/courses/images', [\App\Http\Controllers\Admin\CourseController::class, 'getImages']);
        Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index']);
        Route::post('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'store']);
        Route::get('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'show']);
        Route::put('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update']);
        Route::post('/courses/{course}/reorder', [\App\Http\Controllers\Admin\CourseController::class, 'reorderLessons']);
        Route::delete('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy']);

        Route::post('/courses/{course}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store']);
        Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update']);
        Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy']);

        Route::get('/news', [\App\Http\Controllers\Admin\NewsController::class, 'index']);
        Route::post('/news', [\App\Http\Controllers\Admin\NewsController::class, 'store']);
        Route::get('/news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'show']);
        Route::put('/news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'update']);
        Route::patch('/news/{news}/toggle-publish', [\App\Http\Controllers\Admin\NewsController::class, 'togglePublish']);
        Route::delete('/news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'destroy']);
    });
});

Route::get('/user', function (Request $request): JsonResponse {
    return response()->json($request->user()->load(['profile', 'progress.course.lessons']));
})->middleware('auth:sanctum');

Route::get('/debug-vercel', function (Request $request) {
    return response()->json([
        'db_connection' => config('database.default'),
        'db_database' => config('database.connections.pgsql.database'),
        'db_host' => config('database.connections.pgsql.host'),
        'app_url' => env('APP_URL'),
        'cors_allowed_origins' => config('cors.allowed_origins'),
        'sanctum_stateful_domains' => config('sanctum.stateful'),
        'request_origin' => $request->header('Origin'),
        'request_host' => $request->getHost(),
        'server_vercel' => $_SERVER['VERCEL'] ?? 'NOT SET',
        'env_vercel' => $_ENV['VERCEL'] ?? 'NOT SET',
    ]);
});
