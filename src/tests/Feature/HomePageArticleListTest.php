<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageArticleListTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_lists_only_published_articles(): void
    {
        Article::factory()->published()->create([
            'title' => 'Article visible',
            'slug' => 'article-visible',
            'content' => '<p>Contenu visible.</p>',
        ]);

        Article::factory()->create([
            'title' => 'Article brouillon',
            'slug' => 'article-brouillon',
            'status' => ArticleStatus::Draft,
            'published_at' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Articles')
            ->assertSee('Article visible')
            ->assertDontSee('Article brouillon');
    }

    public function test_home_page_paginates_articles_by_nine(): void
    {
        Article::factory()->count(10)->published()->create();

        $firstPage = $this->get(route('home'));
        $secondPage = $this->get(route('home', ['page' => 2]));

        $firstPage
            ->assertOk()
            ->assertViewHas('articles', fn ($articles) => $articles->count() === 9 && $articles->total() === 10)
            ->assertSee('?page=2', false);

        $secondPage
            ->assertOk()
            ->assertViewHas('articles', fn ($articles) => $articles->count() === 1 && $articles->currentPage() === 2);
    }
}
