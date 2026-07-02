<?php

namespace App\Actions;

use App\Models\User;

class CreateAdminUser
{
    public function handle(string $name, string $email, string $password): User
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'is_admin' => true,
        ]);
    }
}
