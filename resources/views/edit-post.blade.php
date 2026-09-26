@extends('layouts.app')

@section('title', 'Bericht bewerken')

@section('content')
    <a href="/" class="text-sm text-slate-500 transition hover:text-pink-400">&larr; Terug</a>

    <h1 class="mt-4 text-2xl font-semibold tracking-tight text-white">Bericht bewerken</h1>
    <p class="mt-1 text-sm text-slate-500">
        Geplaatst op {{ $post->created_at->format('j F Y') }}
    </p>

    <form method="POST" action="/edit-post/{{ $post->id }}"
          class="mt-8 space-y-4 rounded-xl border border-slate-800 bg-slate-900/60 p-6">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="mb-1.5 block text-sm text-slate-400">Titel</label>
            <input id="title" name="title" type="text" maxlength="120" required
                   value="{{ old('title', $post->title) }}"
                   class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none transition focus:border-pink-400">
            @error('title')
                <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="body" class="mb-1.5 block text-sm text-slate-400">Bericht</label>
            <textarea id="body" name="body" rows="8" maxlength="5000" required
                      class="w-full resize-y rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-slate-100 outline-none transition focus:border-pink-400">{{ old('body', $post->body) }}</textarea>
            @error('body')
                <p class="mt-1.5 text-sm text-rose-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="rounded-lg bg-pink-500 px-5 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-pink-400">
                Wijzigingen opslaan
            </button>
            <a href="/"
               class="rounded-lg border border-slate-700 px-5 py-2.5 text-sm font-semibold text-slate-300 transition hover:border-slate-600">
                Annuleren
            </a>
        </div>
    </form>
@endsection
