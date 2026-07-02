<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_admin_user(): void
    {
        $this->artisan('user:create-admin', [
            'email' => 'admin@example.com',
            '--name' => 'Site Admin',
            '--password' => 'secret-password',
        ])
            ->expectsOutput('Admin user created successfully.')
            ->expectsOutput('Email: admin@example.com')
            ->assertSuccessful();

        $user = User::query()->where('email', 'admin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('Site Admin', $user->name);
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check('secret-password', $user->password));
    }

    public function test_it_fails_when_the_email_already_exists(): void
    {
        User::factory()->create([
            'email' => 'editor@example.com',
            'name' => 'Editor',
        ]);

        $this->artisan('user:create-admin', [
            'email' => 'editor@example.com',
            '--name' => 'Admin Editor',
            '--password' => 'new-password',
        ])
            ->expectsOutput('A user already exists with this email address.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 1);
    }
}
