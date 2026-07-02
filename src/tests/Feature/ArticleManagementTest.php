<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_trying_to_access_article_creation(): void
    {
        $this->get(route('admin.articles.create'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_article_creation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.articles.create'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_draft_article(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Premier article',
            'content' => '<p>Bonjour <strong>tout le monde</strong>.</p>',
            'status' => ArticleStatus::Draft->value,
        ]);

        $article = Article::query()->firstOrFail();

        $response->assertRedirect(route('admin.articles.edit', $article));

        $this->assertSame('premier-article', $article->slug);
        $this->assertSame(ArticleStatus::Draft, $article->status);
        $this->assertNull($article->published_at);
        $this->assertSame($admin->id, $article->author_id);
    }

    public function test_admin_can_create_a_published_article_and_view_it_publicly(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Article publié',
            'content' => '<p>Contenu public.</p>',
            'status' => ArticleStatus::Published->value,
        ]);

        $article = Article::query()->firstOrFail();

        $this->assertSame(ArticleStatus::Published, $article->status);
        $this->assertNotNull($article->published_at);

        $this->get(route('articles.show', $article->slug))
            ->assertOk()
            ->assertSee('Article publié')
            ->assertSee('Contenu public.', false);
    }

    public function test_article_publication_date_is_only_set_once(): void
    {
        Carbon::setTestNow('2026-07-02 12:00:00');

        $admin = User::factory()->create(['is_admin' => true]);
        $article = Article::factory()->create([
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'author_id' => $admin->id,
        ]);

        $this->actingAs($admin)->put(route('admin.articles.update', $article), [
            'title' => $article->title,
            'content' => '<p>Première publication.</p>',
            'status' => ArticleStatus::Published->value,
        ])->assertRedirect(route('admin.articles.edit', $article));

        $article->refresh();
        $firstPublishedAt = $article->published_at;

        $this->assertNotNull($firstPublishedAt);

        Carbon::setTestNow('2026-07-05 08:30:00');

        $this->actingAs($admin)->put(route('admin.articles.update', $article), [
            'title' => $article->title,
            'content' => '<p>Retour en brouillon.</p>',
            'status' => ArticleStatus::Draft->value,
        ])->assertRedirect(route('admin.articles.edit', $article));

        $article->refresh();

        $this->assertSame(ArticleStatus::Draft, $article->status);
        $this->assertTrue($article->published_at?->equalTo($firstPublishedAt));

        Carbon::setTestNow('2026-07-10 18:00:00');

        $this->actingAs($admin)->put(route('admin.articles.update', $article), [
            'title' => $article->title,
            'content' => '<p>Seconde publication.</p>',
            'status' => ArticleStatus::Published->value,
        ])->assertRedirect(route('admin.articles.edit', $article));

        $article->refresh();

        $this->assertSame(ArticleStatus::Published, $article->status);
        $this->assertTrue($article->published_at?->equalTo($firstPublishedAt));

        Carbon::setTestNow();
    }

    public function test_duplicate_titles_generate_unique_slugs(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Article::factory()->create([
            'title' => 'Même titre',
            'slug' => 'meme-titre',
            'author_id' => $admin->id,
        ]);

        $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Même titre',
            'content' => '<p>Autre contenu.</p>',
            'status' => ArticleStatus::Draft->value,
        ]);

        $this->assertDatabaseHas('articles', [
            'slug' => 'meme-titre-2',
        ]);
    }

    public function test_draft_article_is_not_publicly_visible(): void
    {
        $article = Article::factory()->create([
            'slug' => 'brouillon-prive',
            'status' => ArticleStatus::Draft,
            'published_at' => null,
        ]);

        $this->get(route('articles.show', $article->slug))
            ->assertNotFound();
    }

    public function test_article_content_is_sanitized_before_persistence(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Article sécurisé',
            'content' => '<p>Texte</p><script>alert(1)</script><a href="javascript:alert(2)" onclick="x()">Lien</a>',
            'status' => ArticleStatus::Draft->value,
        ]);

        $article = Article::query()->firstOrFail();

        $this->assertStringNotContainsString('<script>', $article->content);
        $this->assertStringNotContainsString('javascript:alert(2)', $article->content);
        $this->assertStringNotContainsString('onclick=', $article->content);
        $this->assertStringContainsString('<p>Texte</p>', $article->content);
    }

    public function test_admin_sees_article_creation_link_in_header(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Nouvel article');
    }
}
