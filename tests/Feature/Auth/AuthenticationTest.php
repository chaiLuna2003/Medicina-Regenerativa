<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_login_muestra_saludo_y_contacto_de_soporte(): void
    {
        Carbon::setTestNow('2026-09-09 15:00:00');

        try {
            $respuesta = $this->get('/login');

            $respuesta
                ->assertOk()
                ->assertSee('Buenas tardes')
                ->assertSee('contacta a soporte')
                ->assertSee(
                    'https://wa.me/5215642859995?text='
                    .'Hola%2C%20tengo%20problemas%20para%20'
                    .'acceder%20a%20mi%20cuenta',
                    false
                )
                ->assertDontSee('¿Olvidaste tu contraseña?')
                ->assertDontSee('Regístrate');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
