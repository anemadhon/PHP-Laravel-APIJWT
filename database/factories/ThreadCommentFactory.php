<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body' => $this->faker->realText(100, 2),
            'thread_id' => Thread::factory()->create()->id,
            'user_id' => User::factory()->create()->id
        ];
    }
}
