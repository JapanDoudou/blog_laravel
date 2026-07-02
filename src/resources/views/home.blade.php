@extends('layouts.app')

@section('content')
    <section class="text-center py-5">
        <h1 class="display-5 fw-semibold">Hello world</h1>
        <p class="lead text-body-secondary mb-0">Base Laravel MVC minimale avec Bootstrap et Docker.</p>
    </section>

    @auth
        @if (auth()->user()->is_admin)
            <section class="text-center">
                <a href="{{ route('admin.articles.create') }}" class="btn btn-dark">Écrire un article</a>
            </section>
        @endif
    @endauth
@endsection
