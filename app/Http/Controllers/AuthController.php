<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', new \App\Rules\NicknameNotProfane()],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'recaptcha_token' => ['required', 'string', new \App\Rules\Recaptcha('register')],
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        \App\Models\Profile::create([
            'user_id' => $user->id,
            'nickname' => $request->name,
            'bio' => 'Student programowania',
            'theme' => 'light',
        ]);

        $user->sendEmailVerificationNotification();

        $this->logAction('register', 'Użytkownik zarejestrował się', [], $user->id, $request->name);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->load('profile'),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'recaptcha_token' => ['required', 'string', new \App\Rules\Recaptcha('login')],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['Nieprawidłowe dane logowania.'],
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return response()->json([
                'message' => 'Email nie został potwierdzony.',
                'email_unverified' => true,
            ], 403);
        }

        if ($user->is_banned) {
            Auth::logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            throw ValidationException::withMessages([
                'email' => ['Nie możesz się zalogować, twoje konto zostało zablokowane.'],
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $this->logAction('login', 'Użytkownik zalogował się');

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('profile'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if (Auth::user()) {
            $this->logAction('logout', 'Użytkownik wylogował się');
        }

        Auth::guard('web')->logout();
        
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user()->load([
            'profile',
            'progress.course' => function ($query) {
                $query->withCount('lessons')->with('lessons:id,course_id,title,order');
            },
        ]));
    }
}
