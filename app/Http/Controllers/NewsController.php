<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function index(): JsonResponse
    {
        $search = request('search');
        $type = request('type', 'all');
        $cacheKey = "news_index_{$search}_{$type}";

        $news = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(10), function () use ($search, $type) {
            $query = Article::with(['user.profile', 'tags'])
                ->where('is_published', true);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhereHas('tags', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($type !== 'all') {
                $query->where('type', $type);
            }

            return $query->orderBy('created_at', 'desc')->get();
        });

        return response()->json(['data' => $news]);
    }

    public function show(string $id): JsonResponse
    {
        $article = Article::with(['user.profile', 'tags'])->findOrFail($id);

        return response()->json($article);
    }
}
