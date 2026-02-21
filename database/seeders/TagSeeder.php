<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'JavaScript', 'Frontend', 'Backend', 'PHP', 'Laravel', 'Vue', 'React', 'Node.js', 'Databases', 'SQL',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(['name' => $tagName]);
        }

        $courses = Course::all();

        foreach ($courses as $course) {
            $title = strtolower($course->title);

            if (str_contains($title, 'javascript') || str_contains($title, 'js')) {
                $courseTags = Tag::whereIn('name', ['JavaScript', 'Frontend'])->get();
                $course->tags()->sync($courseTags);
            } elseif (str_contains($title, 'php')) {
                $courseTags = Tag::whereIn('name', ['PHP', 'Backend', 'Laravel'])->get();
                $course->tags()->sync($courseTags);
            } elseif (str_contains($title, 'node')) {
                $courseTags = Tag::whereIn('name', ['JavaScript', 'Node.js', 'Backend'])->get();
                $course->tags()->sync($courseTags);
            } elseif (str_contains($title, 'react')) {
                $courseTags = Tag::whereIn('name', ['JavaScript', 'React', 'Frontend'])->get();
                $course->tags()->sync($courseTags);
            } elseif (str_contains($title, 'vue')) {
                $courseTags = Tag::whereIn('name', ['JavaScript', 'Vue', 'Frontend'])->get();
                $course->tags()->sync($courseTags);
            } elseif (str_contains($title, 'sql') || str_contains($title, 'bazy danych')) {
                $courseTags = Tag::whereIn('name', ['SQL', 'Databases', 'Backend'])->get();
                $course->tags()->sync($courseTags);
            } else {
                $courseTags = Tag::inRandomOrder()->take(2)->get();
                $course->tags()->sync($courseTags);
            }
        }
    }
}
