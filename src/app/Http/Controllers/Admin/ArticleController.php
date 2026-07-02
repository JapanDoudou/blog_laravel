<?php

namespace App\Http\Controllers\Admin;

use App\Actions\UpsertArticle;
use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', Article::class);

        return view('admin.articles.create', [
            'article' => new Article([
                'status' => ArticleStatus::Draft,
            ]),
            'statuses' => ArticleStatus::cases(),
        ]);
    }

    public function store(ArticleRequest $request, UpsertArticle $upsertArticle): RedirectResponse
    {
        $article = $upsertArticle->handle(
            attributes: $request->validated(),
            author: $request->user(),
        );

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('status', 'Article enregistré.');
    }

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        return view('admin.articles.edit', [
            'article' => $article,
            'statuses' => ArticleStatus::cases(),
        ]);
    }

    public function update(ArticleRequest $request, Article $article, UpsertArticle $upsertArticle): RedirectResponse
    {
        $upsertArticle->handle(
            attributes: $request->validated(),
            author: $request->user(),
            article: $article,
        );

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('status', 'Article mis à jour.');
    }
}
