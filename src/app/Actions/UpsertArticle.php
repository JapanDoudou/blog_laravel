<?php

namespace App\Actions;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UpsertArticle
{
    /**
     * @param  array{title: string, content: string, status: string|ArticleStatus, slug?: string}  $attributes
     */
    public function handle(array $attributes, User $author, ?Article $article = null): Article
    {
        $article ??= new Article();

        $status = $attributes['status'] instanceof ArticleStatus
            ? $attributes['status']
            : ArticleStatus::from($attributes['status']);

        $title = trim($attributes['title']);
        $content = $this->sanitizeHtml($attributes['content']);
        $explicitSlug = isset($attributes['slug']) ? trim($attributes['slug']) : null;

        $article->fill([
            'title' => $title,
            'content' => $content,
            'status' => $status,
        ]);

        if (! $article->exists) {
            $article->author()->associate($author);
        }

        if ($explicitSlug !== null && $explicitSlug !== '') {
            $article->slug = $this->uniqueSlug($explicitSlug, $article);
        } elseif (! $article->exists || $article->isDirty('title')) {
            $article->slug = $this->uniqueSlug($title, $article);
        }

        if ($status === ArticleStatus::Published && $article->published_at === null) {
            $article->published_at = Carbon::now();
        }

        $article->save();

        return $article->refresh();
    }

    private function uniqueSlug(string $title, Article $article): string
    {
        $baseSlug = Str::slug($title, '-', app()->getLocale());
        $slug = $baseSlug !== '' ? $baseSlug : 'article';
        $suffix = 2;

        while (
            Article::query()
                ->where('slug', $slug)
                ->when($article->exists, fn ($query) => $query->whereKeyNot($article->getKey()))
                ->exists()
        ) {
            $slug = $baseSlug !== '' ? $baseSlug.'-'.$suffix : 'article-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function sanitizeHtml(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $encodedHtml = mb_encode_numericentity(
            $html,
            [0x80, 0x10FFFF, 0, 0xFFFF],
            'UTF-8'
        );

        $document->loadHTML(
            '<!DOCTYPE html><html><body>'.$encodedHtml.'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $allowedTags = [
            'a',
            'blockquote',
            'br',
            'em',
            'h2',
            'h3',
            'h4',
            'li',
            'ol',
            'p',
            'strong',
            'ul',
        ];

        $this->sanitizeNode($document->documentElement, $allowedTags);

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body instanceof DOMElement) {
            return '';
        }

        $sanitizedHtml = '';

        foreach ($body->childNodes as $childNode) {
            $sanitizedHtml .= $document->saveHTML($childNode);
        }

        return trim($sanitizedHtml);
    }

    /**
     * @param  array<int, string>  $allowedTags
     */
    private function sanitizeNode(DOMNode $node, array $allowedTags): void
    {
        if ($node instanceof DOMElement) {
            $tag = strtolower($node->tagName);

            if (! in_array($tag, $allowedTags, true) && $tag !== 'html' && $tag !== 'body') {
                $this->unwrapNode($node);

                return;
            }

            $allowedAttributes = $tag === 'a' ? ['href'] : [];

            if ($node->hasAttributes()) {
                for ($index = $node->attributes->length - 1; $index >= 0; $index--) {
                    $attribute = $node->attributes->item($index);

                    if ($attribute === null) {
                        continue;
                    }

                    if (! in_array(strtolower($attribute->name), $allowedAttributes, true)) {
                        $node->removeAttributeNode($attribute);
                    }
                }
            }

            if ($tag === 'a') {
                $href = trim((string) $node->getAttribute('href'));

                if (! $this->isAllowedHref($href)) {
                    $node->removeAttribute('href');
                }
            }
        }

        for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
            $childNode = $node->childNodes->item($index);

            if ($childNode instanceof DOMNode) {
                $this->sanitizeNode($childNode, $allowedTags);
            }
        }
    }

    private function unwrapNode(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if (! $parent instanceof DOMNode) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private function isAllowedHref(string $href): bool
    {
        if ($href === '') {
            return false;
        }

        if (str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }

        return preg_match('/^(https?:|mailto:)/i', $href) === 1;
    }
}
