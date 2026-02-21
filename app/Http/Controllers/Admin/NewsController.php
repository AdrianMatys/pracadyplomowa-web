<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['user.profile', 'tags'])->orderBy('id', 'desc');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->get('type') !== 'all') {
            $query->where('type', $request->get('type'));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(Article $news): JsonResponse
    {
        return response()->json($news->load(['tags']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'estimated_time' => 'nullable|integer',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'type' => 'required|in:news,article',
        ]);

        $article = new Article($validated);
        $article->user_id = auth()->id();
        $article->save();

        if (isset($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }

        \Illuminate\Support\Facades\Cache::flush();

        $this->logAction('admin_article_create', "Admin utworzył artykuł '{$article->title}' (ID: {$article->id})", ['article_id' => $article->id, 'type' => $validated['type']]);

        return response()->json($article, 201);
    }

    public function update(Request $request, Article $news): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'estimated_time' => 'nullable|integer',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'type' => 'required|in:news,article',
        ]);

        $oldTitle = $news->title;
        $news->update($validated);

        if (isset($validated['tags'])) {
            $news->tags()->sync($validated['tags']);
        }

        \Illuminate\Support\Facades\Cache::flush();

        $this->logAction('admin_article_update', "Admin zaktualizował artykuł '{$news->title}' (ID: {$news->id})", ['article_id' => $news->id, 'old_title' => $oldTitle]);

        return response()->json($news);
    }

    public function destroy(Article $news): JsonResponse
    {
        $data = ['article_id' => $news->id, 'article_title' => $news->title];
        $desc = "Admin usunął artykuł '{$news->title}' (ID: {$news->id})";

        $news->tags()->detach();
        $news->delete();

        \Illuminate\Support\Facades\Cache::flush();

        $this->logAction('admin_article_delete', $desc, $data);

        return response()->json(['message' => 'Article deleted successfully']);
    }

    public function togglePublish(Article $news): JsonResponse
    {
        $news->is_published = ! $news->is_published;
        $news->save();

        \Illuminate\Support\Facades\Cache::flush();

        $statusPl = $news->is_published ? 'opublikował' : 'ukrył';
        $this->logAction('admin_article_toggle_publish', "Admin {$statusPl} artykuł '{$news->title}' (ID: {$news->id})", ['article_id' => $news->id, 'is_published' => $news->is_published]);

        return response()->json([
            'message' => 'Article status updated',
            'is_published' => $news->is_published,
        ]);
    }
}
