<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Thread;
use App\Models\ThreadComment;
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
            'slug' => Thread::factory()->create()->id.random_int(1,3).User::factory()->create()->id,
            'thread_id' => Thread::factory()->create()->id,
            'user_id' => User::factory()->create()->id
        ];
    }
}
