<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Thread;
use App\Models\ThreadComment;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_comment_a_thread_before_login()
    {
        $response = $this->postJson(route('threads.comments.store', ['thread' => 'thread']), [
            'body' => 'comment',
            'thread_id' => 1
        ], [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_cannot_comment_a_thread_when_token_wrongs()
    {
        $response = $this->postJson(route('threads.comments.store', ['thread' => 'thread']), [
            'body' => 'comment',
            'thread_id' => 1
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer lplplp'
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_comment_a_thread_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::factory()->create();

        $response = $this->postJson(route('threads.comments.store', ['thread' => $thread]), [
            'body' => 'comment',
            'thread_id' => $thread->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_provide_empty_comment()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::factory()->create();

        $response = $this->postJson(route('threads.comments.store', ['thread' => $thread]), [
            'body' => '',
            'thread_id' => $thread->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_cannot_provide_invalid_comment()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::factory()->create();

        $response = $this->postJson(route('threads.comments.store', ['thread' => $thread]), [
            'body' => 'hi',
            'thread_id' => $thread->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_cannot_comment_a_unknown_thread()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);

        $response = $this->postJson(route('threads.comments.store', ['thread' => 'thread']), [
            'body' => 'hai',
            'thread_id' => 1
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_update_his_comment_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::factory()->create();
        $comment = ThreadComment::create([
            'body' => 'hai there',
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $response = $this->postJson(route('threads.comments.update', ['thread' => $thread, 'comment' => $comment]), [
            'body' => 'hai there again',
            'thread_id' => $thread->id,
            '_method' => 'PUT'
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_update_other_comment()
    {
        $user = User::factory(2)->create();
        $token  = JWTAuth::fromUser($user->first());
        $thread = Thread::factory()->create();
        $comment = ThreadComment::create([
            'body' => 'hai there',
            'thread_id' => $thread->id,
            'user_id' => $user->last()->id
        ]);

        $response = $this->postJson(route('threads.comments.update', ['thread' => $thread, 'comment' => $comment]), [
            'body' => 'hai there again',
            'thread_id' => $thread->id,
            '_method' => 'PUT'
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(403)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_delete_his_comment_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::factory()->create();
        $comment = ThreadComment::create([
            'body' => 'hai there',
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson(route('threads.comments.destroy', ['thread' => $thread, 'comment' => $comment]), ['comment' => $comment], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_delete_other_comment()
    {
        $user = User::factory(2)->create();
        $token  = JWTAuth::fromUser($user->first());
        $thread = Thread::factory()->create();
        $comment = ThreadComment::create([
            'body' => 'hai there',
            'thread_id' => $thread->id,
            'user_id' => $user->last()->id
        ]);

        $response = $this->deleteJson(route('threads.comments.destroy', ['thread' => $thread, 'comment' => $comment]), ['comment' => $comment], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(403)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
}
