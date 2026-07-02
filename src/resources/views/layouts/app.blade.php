<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MyLaravelApp</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <header class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container">
                <span class="navbar-brand mb-0 h1">MyLaravelApp</span>
            </div>
        </header>

        <main class="container py-5">
            @yield('content')
        </main>
    </body>
</html>
