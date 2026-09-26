<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput(['email' => $credentials['email']])
                ->withErrors(['email' => 'Onjuist e-mailadres of wachtwoord.']);
        }

        // Nieuwe sessie-ID, zodat een vastgezet session id niet hergebruikt kan
        // worden. Laravel noemt dit verplicht na inloggen.
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Uitgelogd.');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:60'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($validated);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect('/')->with('status', 'Account aangemaakt. Welkom.');
    }
}
