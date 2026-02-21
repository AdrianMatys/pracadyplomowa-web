<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = \Illuminate\Support\Facades\Cache::remember('tags_all', now()->addDay(), function () {
            return Tag::orderBy('name')->get(['id', 'name']);
        });

        return response()->json($tags);
    }
}
