<!DOCTYPE html>
<html lang="nl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Berichten')</title>
    <meta name="description" content="Een kleine berichtenapplicatie in Laravel, met accounts en berichten die van jezelf blijven.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-950 text-slate-200 antialiased">

    <div class="mx-auto flex min-h-full w-full max-w-3xl flex-col px-5">

        <header class="flex items-center justify-between border-b border-slate-800 py-6">
            <a href="/" class="text-lg font-semibold tracking-tight text-white">
                Bylore<span class="text-pink-400">.</span>berichten
            </a>

            @auth
                <form method="POST" action="/logout">
                    @csrf
                    <span class="mr-4 hidden text-sm text-slate-500 sm:inline">{{ auth()->user()->name }}</span>
                    <button type="submit"
                            class="rounded-full border border-slate-700 px-4 py-1.5 text-sm text-slate-400 transition hover:border-pink-400 hover:text-pink-400">
                        Uitloggen
                    </button>
                </form>
            @else
                <span class="text-sm text-slate-500">Niet ingelogd</span>
            @endauth
        </header>

        @if (session('status'))
            <p class="mt-6 rounded-lg border border-emerald-800 bg-emerald-950/60 px-4 py-3 text-sm text-emerald-300">
                {{ session('status') }}
            </p>
        @endif

        <main class="flex-1 py-10">
            @yield('content')
        </main>

        <footer class="border-t border-slate-800 py-6 text-xs text-slate-600">
            Laravel {{ app()->version() }} &middot; gemaakt als oefenproject
        </footer>
    </div>

</body>
</html>
