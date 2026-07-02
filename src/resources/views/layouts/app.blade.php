<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MyLaravelApp</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell">
        <header class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a href="{{ route('home') }}" class="navbar-brand mb-0 h1 text-decoration-none">MyLaravelApp</a>

                <div class="d-flex align-items-center gap-3">
                    @auth
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.articles.create') }}" class="btn btn-warning btn-sm">Nouvel article</a>
                        @endif

                        <div class="d-flex align-items-center gap-2 text-white">
                            <span class="user-badge" title="{{ auth()->user()->name }}" aria-label="Utilisateur connecté">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5 6a5 5 0 1 1 10 0H3Z"/>
                                </svg>
                            </span>
                            <span class="small">{{ auth()->user()->name }}</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="container py-5">
            @yield('content')
        </main>
    </body>
</html>
