<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Jeśli podany adres email istnieje w naszej bazie, otrzymasz link do resetowania hasła.',
            ]);
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $resetUrl = rtrim(config('app.url'), '/').'/reset-hasla/'.$token;

        Mail::send('emails.password-reset', ['resetUrl' => $resetUrl, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Resetowanie hasła - MasterUrCode');
        });

        $this->logAction('password_reset_link', 'Wysłano link do resetowania hasła', [], $user->id, $user->profile->nickname ?? $user->email);

        return response()->json([
            'message' => 'Jeśli podany adres email istnieje w naszej bazie, otrzymasz link do resetowania hasła.',
        ]);
    }

    public function getEmailByToken(string $token): JsonResponse
    {
        $resetRecord = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (! $resetRecord) {
            return response()->json(['message' => 'Link do resetowania hasła jest nieprawidłowy lub wygasł.'], 400);
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();

            return response()->json(['message' => 'Link do resetowania hasła wygasł. Poproś o nowy link.'], 400);
        }

        $email = $resetRecord->email;
        $parts = explode('@', $email);
        $maskedName = substr($parts[0], 0, 2).str_repeat('*', max(0, strlen($parts[0]) - 2));
        $maskedEmail = $maskedName.'@'.$parts[1];

        return response()->json([
            'email' => $resetRecord->email,
            'maskedEmail' => $maskedEmail,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        if (! $resetRecord) {
            return response()->json(['message' => 'Link do resetowania hasła jest nieprawidłowy lub wygasł.'], 400);
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_reset_tokens')->where('token', $request->token)->delete();

            return response()->json(['message' => 'Link do resetowania hasła wygasł. Poproś o nowy link.'], 400);
        }

        $user = User::where('email', $resetRecord->email)->first();

        if (! $user) {
            return response()->json(['message' => 'Użytkownik nie został znaleziony.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        $this->logAction('password_reset', 'Zresetowano hasło', [], $user->id, $user->profile->nickname ?? $user->email);

        return response()->json(['message' => 'Hasło zostało zmienione. Możesz się teraz zalogować.']);
    }
}
