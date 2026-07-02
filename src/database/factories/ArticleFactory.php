<?php

namespace Database\Factories;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'author_id' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => ArticleStatus::Published,
            'published_at' => now(),
        ]);
    }
}
