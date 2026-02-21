<?php

namespace App\Http\Controllers;

use App\Models\Log;

abstract class Controller
{
    protected function logAction(string $action, string $description, array $metadata = [], ?int $userId = null, ?string $userName = null): void
    {
        Log::create([
            'user_id' => $userId ?? auth()->id(),
            'user_name' => $userName ?? auth()->user()?->profile?->nickname ?? auth()->user()?->email ?? 'System',
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata ?: null,
        ]);
    }
}
