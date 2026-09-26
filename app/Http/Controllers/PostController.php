<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class PostController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:5000'],
        ]);


        $request->user()->posts()->create($validated);

        return redirect('/')->with('status', 'Bericht geplaatst.');
    }

    public function edit(Request $request, Post $post): View
    {
        $this->authorize('update', $post);

        return view('edit-post', ['post' => $post]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $post->update($validated);

        return redirect('/')->with('status', 'Bericht bijgewerkt.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect('/')->with('status', 'Bericht verwijderd.');
    }
}
