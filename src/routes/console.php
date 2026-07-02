<?php

use App\Actions\CreateAdminUser;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:create-admin {email?} {--name=} {--password=}', function (CreateAdminUser $createAdminUser) {
    $email = $this->argument('email') ?? $this->ask('Admin email');
    $name = $this->option('name') ?: $this->ask('Admin name');
    $password = $this->option('password') ?: $this->secret('Admin password');

    if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('The admin email must be a valid email address.');

        return self::FAILURE;
    }

    if (! is_string($name) || trim($name) === '') {
        $this->error('The admin name is required.');

        return self::FAILURE;
    }

    if (! is_string($password) || trim($password) === '') {
        $this->error('The admin password is required.');

        return self::FAILURE;
    }

    $email = strtolower(trim($email));

    if (User::query()->where('email', $email)->exists()) {
        $this->error('A user already exists with this email address.');

        return self::FAILURE;
    }

    $user = $createAdminUser->handle(
        name: trim($name),
        email: $email,
        password: $password,
    );

    $this->info('Admin user created successfully.');
    $this->line(sprintf('Email: %s', $user->email));

    return self::SUCCESS;
})->purpose('Create an admin user');
