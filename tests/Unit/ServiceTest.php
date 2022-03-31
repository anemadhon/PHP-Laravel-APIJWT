<?php

namespace Tests\Unit;

use App\Enums\LikeStatus;
use App\Models\Like;
use App\Models\Thread;
use App\Models\User;
use Tests\TestCase;
use App\Services\LikeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_available_to_like_the_thread_that_no_like_before()
    {
        $user = User::factory()->create();
        $thread = Thread::factory()->create();
        $status = (new LikeService())->checkAvailablityUser($thread->id, $user->id);
        
        $this->assertEquals('available', $status);
    }

    public function test_user_still_available_to_like_the_thread_that_has_likes_before()
    {
        $user = User::factory(2)->create();
        $thread = Thread::factory()->create();

        Like::create([
            'status' => LikeStatus::like,
            'thread_id' => $thread->id,
            'user_id' => $user->first()->id
        ]);

        $status = (new LikeService())->checkAvailablityUser($thread->id, $user->last()->id);

        $this->assertEquals('available', $status);
    }

    public function test_user_not_available_to_like_the_thread_that_liked_by_him_before()
    {
        $user = User::factory(2)->create();
        $thread = Thread::factory()->create();

        Like::create([
            'status' => LikeStatus::like,
            'thread_id' => $thread->id,
            'user_id' => $user->last()->id
        ]);

        $status = (new LikeService())->checkAvailablityUser($thread->id, $user->last()->id);

        $this->assertEquals('not available', $status);
    }
}
