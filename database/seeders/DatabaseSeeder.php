<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CoursesSeeder::class,
            GuestCourseSeeder::class,
            TagSeeder::class,
            AchievementSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
