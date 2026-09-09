<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_is_not_available(): void
    {
        $this
            ->get('/forgot-password')
            ->assertNotFound();
    }

    public function test_password_reset_link_cannot_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this
            ->post('/forgot-password', [
                'email' => $user->email,
            ])
            ->assertNotFound();

        Notification::assertNothingSent();
    }

    public function test_reset_password_screen_is_not_available(): void
    {
        $this
            ->get('/reset-password/token-de-prueba')
            ->assertNotFound();
    }

    public function test_password_cannot_be_reset_through_public_route(): void
    {
        $user = User::factory()->create();
        $passwordOriginal = $user->password;

        $this
            ->post('/reset-password', [
                'token' => 'token-de-prueba',
                'email' => $user->email,
                'password' => 'NuevaPassword2026!',
                'password_confirmation' => 'NuevaPassword2026!',
            ])
            ->assertNotFound();

        $this->assertSame(
            $passwordOriginal,
            $user->fresh()->password
        );
    }
}
