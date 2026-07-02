<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_seeder_imports_json_entries_as_published_articles(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'seed-admin@example.com',
            'is_admin' => true,
        ]);

        $this->assertSame(2, Article::query()->count());

        $article = Article::query()->where('title', 'Bienvenue sur le blog')->firstOrFail();

        $this->assertSame(ArticleStatus::Published, $article->status);
        $this->assertNotNull($article->published_at);
        $this->assertStringContainsString('Premier article', $article->content);
    }
}
