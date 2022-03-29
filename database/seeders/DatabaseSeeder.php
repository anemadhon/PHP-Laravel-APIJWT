<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(20)->create();
        \App\Models\Thread::factory(8)->create();
        \App\Models\ThreadComment::factory(3)->create();
        \App\Models\Like::factory(5)->create();
    }
}
