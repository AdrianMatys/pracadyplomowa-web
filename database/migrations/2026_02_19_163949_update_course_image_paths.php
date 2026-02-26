<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('courses')
            ->where('image_path', 'like', 'courses/%')
            ->update([
                'image_path' => DB::raw("REPLACE(image_path, 'courses/', 'images/courses/')"),
            ]);

        DB::table('profiles')
            ->where('avatar_url', 'like', 'avatars/%')
            ->update([
                'avatar_url' => DB::raw("REPLACE(avatar_url, 'avatars/', 'images/avatars/')"),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('courses')
            ->where('image_path', 'like', 'images/courses/%')
            ->update([
                'image_path' => DB::raw("REPLACE(image_path, 'images/courses/', 'courses/')"),
            ]);

        DB::table('profiles')
            ->where('avatar_url', 'like', 'images/avatars/%')
            ->update([
                'avatar_url' => DB::raw("REPLACE(avatar_url, 'images/avatars/', 'avatars/')"),
            ]);
    }
};
