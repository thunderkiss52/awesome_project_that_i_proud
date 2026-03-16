<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_jwt_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Mikhail',
            'email' => 'mikhail@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'User registered successfully')
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in',
                'user' => ['id', 'name', 'email', 'subscription'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'mikhail@example.com',
        ]);
    }

    public function test_user_can_login_and_fetch_profile(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Mikhail',
            'email' => 'mikhail@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'mikhail@example.com',
            'password' => 'secret123',
        ]);

        $token = $loginResponse->json('token');

        $loginResponse
            ->assertOk()
            ->assertJsonPath('message', 'User logged in successfully');

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'mikhail@example.com');
    }

    public function test_login_is_rate_limited_after_five_requests_per_minute(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/auth/login', [
                'email' => 'unknown@example.com',
                'password' => 'wrong-password',
            ])->assertUnauthorized();
        }

        $this->postJson('/api/auth/login', [
            'email' => 'unknown@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }
}
