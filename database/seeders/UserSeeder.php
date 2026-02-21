<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@learnprogramming.com')->exists()) {
            return;
        }

        $user = User::create([
            'email' => 'admin@learnprogramming.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'nickname' => 'AdminCode',
            'bio' => 'Główny administrator i twórca kursów.',
            'theme' => 'dark',
            'experience_points' => 0,
        ]);
    }
}
