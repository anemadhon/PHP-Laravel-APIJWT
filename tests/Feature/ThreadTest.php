<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_threads_module_before_login()
    {
        $response = $this->getJson(route('threads.index'), [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_cannot_access_threads_module_when_token_wrongs()
    {
        $response = $this->getJson(route('threads.index'), [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer kokgodfgd'
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_get_all_threads_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        
        Thread::factory(5)->create();

        $response = $this->getJson(route('threads.index'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_can_get_all_threads_with_pagination_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        
        Thread::factory(10)->create();

        $response = $this->getJson(route('threads.index'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'links', 'meta'
                ]);
    }
    
    public function test_user_can_get_single_thread_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        
        Thread::factory(5)->create();

        $response = $this->getJson(route('threads.index'), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_get_404_when_access_unknown_threads()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        
        Thread::factory(5)->create();

        $response = $this->getJson(route('threads.show', ['thread' => 'koko']), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_can_post_the_thread_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);

        $response = $this->postJson(route('threads.store'), [
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem',
            'category' => 'post'
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_post_the_empty_thread()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);

        $response = $this->postJson(route('threads.store'), [
            'title' => '',
            'body' => '',
            'category' => '',
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_cannot_post_the_invalid_data()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);

        $response = $this->postJson(route('threads.store'), [
            'title' => '12',
            'body' => '12',
            'category' => '21',
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    
    public function test_user_can_post_the_thread_with_thumbnail_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);

        Storage::fake('public');

        $response = $this->postJson(route('threads.store'), [
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem',
            'category' => 'post',
            'thumbnail' => UploadedFile::fake()->image('thumbnail.png')
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        Storage::disk('public')->assertExists('images/thumbnail/abcdef/thumbnail.png');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    } 
    
    public function test_user_cannot_post_the_thread_with_invalid_thumbnail()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);

        Storage::fake('public');

        $response = $this->postJson(route('threads.store'), [
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem',
            'category' => 'post',
            'thumbnail' => UploadedFile::fake()->image('thumbnail.pdf') 
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        Storage::disk('public')->assertMissing('images/thumbnail/abcdef/thumbnail.pdf');

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    } 
    
    public function test_user_can_update_his_thread_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            'user_id' => $user->id
        ]);

        $response = $this->postJson(route('threads.update', ['thread' => $thread]), [
            'title' => $thread->title,
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
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
    
    public function test_user_cannot_update_other_user_thread()
    {
        $user = User::factory(2)->create();
        $token  = JWTAuth::fromUser($user->first());
        $thread = Thread::factory()->create();

        $response = $this->postJson(route('threads.update', ['thread' => $thread]), [
            'title' => 'abcdefg',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            '_method' => 'PUT',
            'user_id' => $user->last()->id
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(403)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_can_delete_his_thread_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        $thread = Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem update',
            'category' => 'post',
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson(route('threads.destroy', ['thread' => $thread]), ['thread' => $thread], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_cannot_delete_other_user_thread()
    {
        $user = User::factory(2)->create();
        $token  = JWTAuth::fromUser($user->first());
        $threads = Thread::create([
            'title' => 'abcdef',
            'body' => 'lorem lorem lorem',
            'category' => 'post',
            'user_id' => $user->last()->id
        ]);

        $response = $this->deleteJson(route('threads.destroy', ['thread' => $threads->first()]), ['thread' => $threads], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(403)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_get_all_threads_by_given_category_normally()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        
        $threads = Thread::factory(5)->create();

        $response = $this->getJson(route('threads.by.category', ['category' => $threads->first()->category]), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data'
                ]);
    }
    
    public function test_user_get_404_for_unknown_category()
    {
        $user = User::factory()->create();
        $token  = JWTAuth::fromUser($user);
        
        Thread::factory(5)->create();

        $response = $this->getJson(route('threads.by.category', ['category' => 'lplplp']), [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(404)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
}
