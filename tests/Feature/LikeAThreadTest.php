<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Like;
use App\Models\User;
use App\Models\Thread;
use App\Enums\LikeStatus;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeAThreadTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_cannot_like_a_thread_before_login()
    {
        $thread = Thread::factory()->create();

        $response = $this->postJson(route('threads.likes', ['thread' => $thread]), [
            'status' => LikeStatus::like,
            'thread_id' => $thread->id,
            'user_id' => 1
        ], [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_can_like_a_thread_once_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::factory()->create();

        $response = $this->postJson(route('threads.likes', ['thread' => $thread]), [
            'status' => LikeStatus::like,
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_like_a_thread_twice_or_more()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::factory()->create();

        Like::create([
            'status' => LikeStatus::like,
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $response = $this->postJson(route('threads.likes', ['thread' => $thread]), [
            'status' => LikeStatus::like,
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => "You're Like this Thread before",
                    'data' => null
                ]);
    }
    
    public function test_user_cannot_like_a_unknown_thread()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);

        $response = $this->postJson(route('threads.likes', ['thread' => 'koksdds']), [
            'status' => LikeStatus::like,
            'thread_id' => 1,
            'user_id' => $user->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
}
