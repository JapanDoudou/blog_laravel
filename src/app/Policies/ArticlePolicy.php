<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, Article $article): bool
    {
        return (bool) $user->is_admin;
    }
}
