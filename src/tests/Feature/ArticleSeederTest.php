<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ArticleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_seeder_imports_json_entries_as_published_articles(): void
    {
        $this->seed();
        $entries = json_decode(
            (string) File::get(database_path('seeders/data/articles.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertDatabaseHas('users', [
            'email' => 'seed-admin@example.com',
            'is_admin' => true,
        ]);

        $this->assertCount(count($entries), Article::query()->get());

        $firstEntry = $entries[0];
        $article = Article::query()->where('slug', $firstEntry['slug'])->firstOrFail();

        $this->assertSame(ArticleStatus::Published, $article->status);
        $this->assertNotNull($article->published_at);
        $this->assertSame($firstEntry['title'], $article->title);
        $this->assertSame($firstEntry['slug'], $article->slug);
        $this->assertSame('seed-admin@example.com', $article->author->email);
    }

    public function test_article_seeder_is_idempotent_with_slug_based_matching(): void
    {
        $this->seed();
        $countAfterFirstSeed = Article::query()->count();

        $this->seed();

        $this->assertSame($countAfterFirstSeed, Article::query()->count());
    }
}
