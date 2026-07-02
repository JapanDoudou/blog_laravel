@extends('layouts.app')

@section('content')
    <article class="article-page mx-auto">
        <header class="mb-5">
            <p class="text-uppercase text-body-secondary small mb-2">
                Publié le {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
            </p>
            <h1 class="display-5 fw-semibold mb-0">{{ $article->title }}</h1>
        </header>

        <div class="article-content">
            {!! $article->content !!}
        </div>
    </article>
@endsection
