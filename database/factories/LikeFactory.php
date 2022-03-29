<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => random_int(0, 1),
            'user_id' => User::factory()->create()->id,
            'thread_id' => Thread::factory()->create()->id
        ];
    }
}
