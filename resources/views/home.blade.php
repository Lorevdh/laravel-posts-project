@extends('layouts.app')

@section('title', 'Berichten')

@section('content')
    @auth
        <h1 class="text-2xl font-semibold tracking-tight text-white">Je berichten</h1>

        {{-- Nieuw bericht --}}
        <section class="mt-8 rounded-xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-sm font-medium tracking-wide text-slate-400 uppercase">Nieuw bericht</h2>

            <form method="POST" action="/create-post" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="title" class="mb-1.5 block text-sm text-slate-400">Titel</label>
                    <input id="title" name="title" type="text" maxlength="120" value="{{ old('title') }}"
                           placeholder="Waar gaat het over?"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">
                    @error('title')
                        <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body" class="mb-1.5 block text-sm text-slate-400">Bericht</label>
                    <textarea id="body" name="body" rows="4" maxlength="5000"
                              placeholder="Schrijf je bericht..."
                              class="w-full resize-y rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="rounded-lg bg-pink-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-pink-400">
                    Plaatsen
                </button>
            </form>
        </section>

        {{-- Bestaande berichten --}}
        <section class="mt-10">
            <h2 class="text-sm font-medium tracking-wide text-slate-400 uppercase">
                {{ $posts->count() }} {{ $posts->count() === 1 ? 'bericht' : 'berichten' }}
            </h2>

            @forelse ($posts as $post)
                <article class="mt-4 rounded-xl border border-slate-800 bg-slate-900/60 p-6 transition hover:border-slate-700">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <h3 class="text-lg font-semibold text-white">{{ $post->title }}</h3>
                        <time datetime="{{ $post->created_at->toDateString() }}"
                              class="text-xs text-slate-500">
                            {{ $post->created_at->diffForHumans() }}
                        </time>
                    </div>

                    <p class="mt-3 leading-relaxed whitespace-pre-line text-slate-300">{{ $post->body }}</p>

                    <div class="mt-5 flex gap-4 text-sm">
                        <a href="/edit-post/{{ $post->id }}"
                           class="text-slate-400 transition hover:text-pink-400">Bewerken</a>

                        <form method="POST" action="/delete-post/{{ $post->id }}"
                              onsubmit="return confirm('Dit bericht echt verwijderen? Dit kan niet ongedaan gemaakt worden.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 transition hover:text-rose-400">
                                Verwijderen
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="mt-4 rounded-xl border border-dashed border-slate-800 p-10 text-center text-slate-500">
                    Nog geen berichten. Plaats hierboven je eerste.
                </p>
            @endforelse
        </section>
    @else
        <h1 class="text-2xl font-semibold tracking-tight text-white">Berichten</h1>
        <p class="mt-2 text-slate-400">
            Maak een account aan of log in om berichten te plaatsen.
        </p>

        @if ($errors->any())
            <ul class="mt-6 space-y-1 rounded-lg border border-rose-900 bg-rose-950/50 p-4 text-sm text-rose-300">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        {{-- Inloggen --}}
        <section class="mt-8 rounded-xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-sm font-medium tracking-wide text-slate-400 uppercase">Inloggen</h2>

            <form method="POST" action="/login" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="login-email" class="mb-1.5 block text-sm text-slate-400">E-mailadres</label>
                    <input id="login-email" name="email" type="email" autocomplete="email" required
                           value="{{ old('email') }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">
                </div>

                <div>
                    <label for="login-password" class="mb-1.5 block text-sm text-slate-400">Wachtwoord</label>
                    <input id="login-password" name="password" type="password" autocomplete="current-password" required
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-400">
                    <input type="checkbox" name="remember" value="1"
                           class="rounded border-slate-600 bg-slate-950 text-pink-500 focus:ring-pink-400">
                    Onthoud mij
                </label>

                <button type="submit"
                        class="rounded-lg bg-pink-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-pink-400">
                    Inloggen
                </button>
            </form>

            <p class="mt-4 border-t border-slate-800 pt-4 text-xs text-slate-500">
                Probeeraccount: <code class="text-slate-400">demo@bylore.test</code> met wachtwoord
                <code class="text-slate-400">portfolio123</code>
            </p>
        </section>

        {{-- Account aanmaken --}}
        <section class="mt-4 rounded-xl border border-slate-800 bg-slate-900/60 p-6">
            <h2 class="text-sm font-medium tracking-wide text-slate-400 uppercase">Account aanmaken</h2>

            <form method="POST" action="/register" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1.5 block text-sm text-slate-400">Naam</label>
                    <input id="name" name="name" type="text" autocomplete="name" required
                           maxlength="60" value="{{ old('name') }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">
                    @error('name')
                        <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="register-email" class="mb-1.5 block text-sm text-slate-400">E-mailadres</label>
                    <input id="register-email" name="email" type="email" autocomplete="email" required
                           value="{{ old('email') }}"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">
                    @error('email')
                        <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="register-password" class="mb-1.5 block text-sm text-slate-400">
                        Wachtwoord <span class="text-slate-600">(minimaal 8 tekens)</span>
                    </label>
                    <input id="register-password" name="password" type="password"
                           autocomplete="new-password" required minlength="8"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">
                    @error('password')
                        <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="register-password-confirm" class="mb-1.5 block text-sm text-slate-400">Herhaal wachtwoord</label>
                    <input id="register-password-confirm" name="password_confirmation" type="password"
                           autocomplete="new-password" required minlength="8"
                           class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 placeholder-slate-600 outline-none transition focus:border-pink-400">
                </div>

                <button type="submit"
                        class="rounded-lg border border-slate-700 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:border-pink-400 hover:text-pink-400">
                    Account aanmaken
                </button>
            </form>
        </section>
    @endauth
@endsection
