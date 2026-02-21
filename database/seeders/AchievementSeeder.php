<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'id' => 'first_steps',
                'title' => 'Pierwsze Kroki',
                'title_en' => 'First Steps',
                'type' => 'Początek',
                'level' => 'Łatwy',
                'description' => 'Ukończ swoją pierwszą lekcję na platformie.',
                'description_en' => 'Complete your first lesson on the platform.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'code_master',
                'title' => 'Mistrz Kodu',
                'title_en' => 'Code Master',
                'type' => 'Zadania',
                'level' => 'Trudny',
                'description' => 'Rozwiąż 50 zadań bezbłędnie za pierwszym razem.',
                'description_en' => 'Solve 50 tasks flawlessly on the first try.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'night_owl',
                'title' => 'Nocny Marek',
                'title_en' => 'Night Owl',
                'type' => 'Aktywność',
                'level' => 'Średni',
                'description' => 'Ukończ lekcję między godziną 22:00 a 4:00.',
                'description_en' => 'Complete a lesson between 10 PM and 4 AM.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'fast_learner',
                'title' => 'Szybki Uczeń',
                'title_en' => 'Fast Learner',
                'type' => 'Nauka',
                'level' => 'Łatwy',
                'description' => 'Ukończ cały kurs w mniej niż tydzień.',
                'description_en' => 'Complete an entire course in less than a week.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('achievements')->upsert($achievements, ['id'], ['title', 'title_en', 'description', 'description_en', 'updated_at']);

        $user = User::first();
        if ($user) {
            DB::table('achievement_user')->insert([
                [
                    'user_id' => $user->id,
                    'achievement_id' => 'first_steps',
                    'earned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $user->id,
                    'achievement_id' => 'night_owl',
                    'earned_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
