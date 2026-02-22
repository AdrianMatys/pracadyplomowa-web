<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, string $id, string $hash): JsonResponse
    {
        $user = \App\Models\User::findOrFail($id);

        $frontendUrl = rtrim(config('app.url', 'http://localhost:8000'), '/');

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect($frontendUrl . '/logowanie?verified=0&error=invalid_link');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect($frontendUrl . '/logowanie?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect($frontendUrl . '/logowanie?verified=1');
    }

    public function resend(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Nieprawidłowe dane.'], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email już zweryfikowany.']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email weryfikacyjny wysłany.']);
    }
}
