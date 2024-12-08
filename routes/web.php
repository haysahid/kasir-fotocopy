<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });

Route::get('/', fn() => Inertia::render('HomeView'))->name('home');
Route::get('/login', fn()  => Inertia::render('LoginView'))->name('login');
Route::get('/register', fn()  => Inertia::render('RegisterView'))->name('register');
