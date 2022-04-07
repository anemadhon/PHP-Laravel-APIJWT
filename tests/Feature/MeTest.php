<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Like;
use App\Models\User;
use App\Models\Thread;
use App\Enums\LikeStatus;
use App\Models\ThreadComment;
use Illuminate\Http\UploadedFile;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_me_module_before_login()
    {
        $response = $this->getJson(route('me.profile'), [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_access_his_profile_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->getJson(route('me.profile'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_can_update_his_profile_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(route('me.profile'), [
            '_method' => 'PUT',
            'name' => 'Me Again',
            'username' => 'me_again',
            'email' => $user->email
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_update_his_profile_with_empty_data()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(route('me.profile'), [
            '_method' => 'PUT',
            'name' => '',
            'username' => '',
            'email' => ''
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_cannot_update_his_profile_with_invalid_data()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(route('me.profile'), [
            '_method' => 'PUT',
            'name' => 'me',
            'username' => 'Me',
            'email' => $user->email
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_update_his_profile_normally_with_avatar()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Storage::fake('public');

        $response = $this->postJson(route('me.profile'), [
            '_method' => 'PUT',
            'name' => 'Me Again',
            'username' => 'meagain',
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('avatar.png')
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        Storage::disk('public')->assertExists('images/avatar/meagain/avatar.png');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_update_his_profile_with_invalid_avatar()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Storage::fake('public');

        $response = $this->postJson(route('me.profile'), [
            '_method' => 'PUT',
            'name' => 'Me Again',
            'username' => 'meagain',
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('avatar.pdf')
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        Storage::disk('public')->assertMissing('images/avatar/meagain/avatar.pdf');

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_access_his_threads_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            'user_id' => $user->id
        ]);

        $response = $this->getJson(route('me.threads'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }

    public function test_user_can_access_his_threads_with_pagination_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            'user_id' => $user->id
        ]);

        $response = $this->getJson(route('me.threads'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'links', 'meta'
                ]);
    }
    
    public function test_user_can_access_the_threads_he_likes_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $thread = Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            'user_id' => $user->id
        ]);

        Like::create([
            'status' => LikeStatus::like,
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $response = $this->getJson(route('me.threads.likes'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_can_access_the_threads_he_dislikes_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $thread = Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            'user_id' => $user->id
        ]);

        Like::create([
            'status' => LikeStatus::unlike,
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $response = $this->getJson(route('me.threads.likes'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_can_access_the_threads_he_comment_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $thread = Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            'user_id' => $user->id
        ]);

        ThreadComment::create([
            'body' => 'hai there',
            'thread_id' => $thread->id,
            'user_id' => $user->id
        ]);

        $response = $this->getJson(route('me.comments'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
}

