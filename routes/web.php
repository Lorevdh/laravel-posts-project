<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $posts = auth()->check()
        ? auth()->user()->posts()->latest()->get()
        : collect();

    return view('home', ['posts' => $posts]);
})->name('home');

Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::post('/create-post', [PostController::class, 'create'])->name('posts.store');
    Route::get('/edit-post/{post}', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/edit-post/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/delete-post/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});
