<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginname' => 'required',      // dit is nu email
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt([
            'email' => $incomingFields['loginname'],   // BELANGRIJK: email i.p.v. name
            'password' => $incomingFields['loginpassword']
        ])) {
            $request->session()->regenerate();
        }

        return redirect('/');
    }

    public function logout() {
        auth()->guard()->logout();
        return redirect('/');
    }

    public function register(Request $request) {
        $incomingFields = $request->validate([
            'name' => ['required', 'min:3', 'max:10', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'max:200']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);

        auth()->guard()->login($user);

        return redirect('/');
    }
}
