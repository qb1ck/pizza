<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '0123456789',
            'address' => 'Test Address',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
        ]);
    }

    #[Test]
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token']);
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }
}
