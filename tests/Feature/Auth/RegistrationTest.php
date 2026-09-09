<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_is_not_available(): void
    {
        $this
            ->get('/register')
            ->assertNotFound();

        $this->assertGuest();
    }

    public function test_users_cannot_register_publicly(): void
    {
        $this
            ->post('/register', [
                'name' => 'Usuario no autorizado',
                'email' => 'publico@example.com',
                'password' => 'SecureTest2026!',
                'password_confirmation' => 'SecureTest2026!',
            ])
            ->assertNotFound();

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'publico@example.com',
        ]);

        $this->assertSame(
            0,
            User::query()
                ->where('email', 'publico@example.com')
                ->count()
        );
    }
}
