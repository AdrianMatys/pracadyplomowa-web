<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->is_banned || ! $user->exists())) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account is disabled or does not exist.'], 401);
            }
            auth()->logout();

            return redirect('/login');
        }

        return $next($request);
    }
}
