<?php

use App\Http\Controllers\AuthController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::get('/{any}', function (): View {
    return view('app');
})->where('any', '.*');
