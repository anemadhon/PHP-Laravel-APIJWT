<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->catchPhrase();

        return [
            'title' => $title,
            'body' => $this->faker->realText(200, 2),
            'category' => $this->faker->word(),
            'slug' => Str::slug($title),
            'user_id' => User::factory()->create()->id
        ];
    }
}
