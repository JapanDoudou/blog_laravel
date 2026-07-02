@extends('layouts.app')

@section('content')
    <section class="py-4">
        <h1 class="display-6 fw-semibold mb-0">Articles</h1>
    </section>

    @auth
        @if (auth()->user()->is_admin)
            <section class="mb-5">
                <a href="{{ route('admin.articles.create') }}" class="btn btn-dark">Écrire un article</a>
            </section>
        @endif
    @endauth

    @if ($articles->isEmpty())
        <section class="py-5 text-center text-body-secondary">
            <p class="mb-0">Aucun article publié pour le moment.</p>
        </section>
    @else
        <section class="article-list">
            <div class="row g-4 row-cols-1 row-cols-lg-3">
                @foreach ($articles as $article)
                    <div class="col">
                        <article class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <p class="text-body-secondary small text-uppercase mb-2">
                                    {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
                                </p>
                                <h2 class="h4 card-title mb-3">
                                    <a href="{{ route('articles.show', $article->slug) }}" class="stretched-link link-dark text-decoration-none">
                                        {{ $article->title }}
                                    </a>
                                </h2>
                                <p class="card-text text-body-secondary mb-0">
                                    {{ \Illuminate\Support\Str::limit(trim(strip_tags($article->content)), 150) }}
                                </p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $articles->links('pagination::bootstrap-5') }}
            </div>
        </section>
    @endif
@endsection
