<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('welcome');
});

// Login page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Signup page
Route::get('/signup', function () {
    return view('auth.signup');
})->name('signup');

// Google OAuth callback (receives token and stores it)
Route::get('/auth/callback', function () {
    return view('auth.callback');
})->name('auth.callback');

/*
|--------------------------------------------------------------------------
| Protected Pages (Frontend-protected via JS token check)
|--------------------------------------------------------------------------
*/

// User dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
