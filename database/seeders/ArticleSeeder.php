<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        if (Tag::count() === 0) {
            $this->call(TagSeeder::class);
        }

        $userId = \App\Models\User::first()->id ?? 1;

        Article::create([
            'user_id' => $userId,
            'title' => 'Witaj na nowej platformie!',
            'content' => 'Cieszymy się, że jesteś z nami. Rozpocznij naukę już dziś wybierając jeden z dostępnych kursów.',
            'is_published' => true,
        ]);

        Article::create([
            'user_id' => $userId,
            'title' => 'Aktualizacja kursu React',
            'content' => 'Dodaliśmy nowe lekcje dotyczące React Hooks. Sprawdź sekcję z kursami.',
            'is_published' => true,
        ]);

        Article::create([
            'user_id' => $userId,
            'title' => 'Nadchodzące funkcjonalności',
            'content' => 'Pracujemy nad systemem wyzwań i rankingów. Wkrótce więcej informacji!',
            'is_published' => true,
        ]);

        Article::factory()
            ->count(10)
            ->create()
            ->each(function ($article) {
                $tags = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id');
                $article->tags()->attach($tags);
            });
    }
}
