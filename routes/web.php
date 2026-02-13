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

// Auth page (Login / Register / Google)
Route::get('/auth', function () {
    return view('auth.auth');
})->name('auth');

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
