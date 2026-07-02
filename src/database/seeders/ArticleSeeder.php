<?php

namespace Database\Seeders;

use App\Actions\UpsertArticle;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

class ArticleSeeder extends Seeder
{
    public function __construct(
        private readonly UpsertArticle $upsertArticle,
    ) {
    }

    public function run(): void
    {
        $entries = $this->loadEntries();
        $author = $this->resolveAuthor();

        foreach ($entries as $entry) {
            $article = Article::query()
                ->where('slug', $entry['slug'])
                ->first();

            $this->upsertArticle->handle(
                attributes: [
                    'slug' => $entry['slug'],
                    'title' => $entry['title'],
                    'content' => $entry['content'],
                    'status' => ArticleStatus::Published,
                ],
                author: $author,
                article: $article,
            );
        }
    }

    /**
     * @return array<int, array{slug: string, title: string, content: string}>
     */
    private function loadEntries(): array
    {
        $path = database_path('seeders/data/articles.json');

        if (! File::exists($path)) {
            throw new RuntimeException(sprintf('Missing article seed file at [%s].', $path));
        }

        try {
            $entries = json_decode((string) File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('The article seed file contains invalid JSON.', previous: $exception);
        }

        if (! is_array($entries)) {
            throw new RuntimeException('The article seed file must contain a top-level JSON array.');
        }

        $slugs = [];

        foreach ($entries as $index => $entry) {
            if (
                ! is_array($entry)
                || ! is_string($entry['slug'] ?? null)
                || trim($entry['slug']) === ''
                || ! is_string($entry['title'] ?? null)
                || trim($entry['title']) === ''
                || ! is_string($entry['content'] ?? null)
                || trim($entry['content']) === ''
            ) {
                throw new RuntimeException(sprintf(
                    'Invalid article seed entry at index %d. Expected {"slug": "...", "title": "...", "content": "..."} with non-empty strings.',
                    $index
                ));
            }

            $slug = trim($entry['slug']);

            if (in_array($slug, $slugs, true)) {
                throw new RuntimeException(sprintf(
                    'Duplicate article slug [%s] in seed file at index %d.',
                    $slug,
                    $index
                ));
            }

            $slugs[] = $slug;
        }

        /** @var array<int, array{slug: string, title: string, content: string}> $entries */
        return $entries;
    }

    private function resolveAuthor(): User
    {
        return User::query()->firstOrCreate(
            ['email' => 'seed-admin@example.com'],
            [
                'name' => 'Seed Admin',
                'password' => 'password',
                'is_admin' => true,
            ],
        );
    }
}
