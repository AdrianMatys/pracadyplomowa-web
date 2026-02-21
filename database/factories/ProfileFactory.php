<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nickname' => fake()->userName(),
            'bio' => fake()->sentence(),
            'theme' => 'light',
            'experience_points' => 0,
            'streak' => 0,
        ];
    }
}
