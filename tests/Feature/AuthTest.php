<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_login_normally()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ], ['Accept' => 'application/json']);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'token'
                ]);
    }

    public function test_user_provide_the_empty_data()
    {
        $response = $this->postJson(route('login'), [
            'email' => '',
            'password' => ''
        ], ['Accept' => 'application/json']);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_provide_the_wrong_email_format()
    {
        $response = $this->postJson(route('login'), [
            'email' => 'hdjhaj',
            'password' => 'password'
        ], ['Accept' => 'application/json']);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_provide_the_wrong_password()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'passwordd'
        ], ['Accept' => 'application/json']);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_provide_the_unregistered_user()
    {
        $response = $this->postJson(route('login'), [
            'email' => 'a@a.com',
            'password' => 'password'
        ], ['Accept' => 'application/json']);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }

    public function test_user_can_logout_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(route('auth.logout'), [], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(204);
    }

    public function test_user_cannot_logout_before_login()
    {
        $response = $this->postJson(route('auth.logout'));

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_cannot_logout_when_token_wrongs()
    {
        $response = $this->postJson(route('auth.logout'), [], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer y53634tg"
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_can_refresh_token_normally()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(route('auth.refresh'), [], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token}"
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'token'
                ]);
    }

    public function test_user_cannot_refresh_token_before_login()
    {
        $response = $this->postJson(route('auth.refresh'));

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
    
    public function test_user_cannot_refresh_token_when_token_wrongs()
    {
        $response = $this->postJson(route('auth.refresh'), [], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer y3563ghe"
        ]);

        $response->assertStatus(401)
                ->assertJsonStructure([
                    'success', 'message', 'data', 'errors'
                ]);
    }
}
