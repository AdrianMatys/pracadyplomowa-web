<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = \App\Models\Log::query()->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate($request->input('per_page', 50)));
    }

    public function clear(Request $request): JsonResponse
    {
        $request->validate(['password' => 'required|string']);

        if (! \Illuminate\Support\Facades\Hash::check($request->password, $request->user()->password)) {
            return response()->json(['message' => 'Podane hasło jest nieprawidłowe.'], 422);
        }

        \App\Models\Log::truncate();

        $this->logAction('logs_cleared', 'Wyczyszczono wszystkie logi systemowe z bazy danych.');

        return response()->json(['message' => 'Logi zostały wyczyszczone pomyślnie.']);
    }
}
