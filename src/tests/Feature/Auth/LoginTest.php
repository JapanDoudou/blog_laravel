<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible_for_guests(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('Email');
    }

    public function test_user_can_login_and_see_user_icon_in_header(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->get('/')
            ->assertOk()
            ->assertSee('Utilisateur connecté', false)
            ->assertSee('Jane Doe');
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->from('/login')->post('/login', [
                'email' => 'jane@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->from('/login')->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');
    }

    public function test_successful_logins_do_not_consume_the_rate_limit(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ]);

        foreach (range(1, 6) as $attempt) {
            $response = $this->post('/login', [
                'email' => 'jane@example.com',
                'password' => 'secret123',
            ]);

            $response->assertRedirect('/');
            $this->assertAuthenticatedAs($user);

            $this->post('/logout')->assertRedirect('/login');
            $this->assertGuest();
        }
    }
}
