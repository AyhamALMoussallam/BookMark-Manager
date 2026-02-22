<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;

// ------------------------------
// Public Routes
// ------------------------------

// Sign Up
Route::post('/signup', [UserController::class, 'signup']);

// Login
Route::post('/login', [UserController::class, 'login']);

// Public routes for Google Login
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// -------- Email Verification Routes --------

// Verify email
Route::get('/email/verify/{id}/{hash}',
    [EmailVerificationController::class, 'verify']
)->middleware('signed')->name('verification.verify');

Route::post('/email/verification-notification',
    [EmailVerificationController::class, 'resend']
)->middleware('throttle:6,1');

// -------- Password Reset (no auth required) --------
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// ------------------------------
// Protected Routes (Require Auth via Sanctum)
// ------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // -------- User Routes --------
    Route::get('/user', [UserController::class, 'getCurrentUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/profile/password', [UserController::class, 'changePassword']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/profile', [UserController::class, 'deleteAccount']);

    // -------- Bookmark Routes --------

    // Get favorite bookmarks
    Route::get('/bookmarks/favorites', [BookmarkController::class, 'favorites']);

    // CRUD for bookmarks
    Route::apiResource('bookmarks', BookmarkController::class);

    // Favorite / Unfavorite / Toggle Favorite
    Route::post('/bookmarks/{bookmark}/favorite', [BookmarkController::class, 'favorite']);
    Route::post('/bookmarks/{bookmark}/unfavorite', [BookmarkController::class, 'unfavorite']);
    Route::post('/bookmarks/{bookmark}/toggle-favorite', [BookmarkController::class, 'toggleFavorite']);

    // -------- Collection Routes --------

    // CRUD for collections
    Route::apiResource('collections', CollectionController::class);

    // Add / Remove bookmarks to/from collections
    Route::post('/collections/{collection}/bookmarks/{bookmark}', [CollectionController::class, 'addBookmark']);
    Route::delete('/collections/{collection}/bookmarks/{bookmark}', [CollectionController::class, 'removeBookmark']);

    // -------- Tag Routes --------

    // Get all tags
    Route::get('/tags', [TagController::class, 'index']);

    // Update tag
    Route::put('/tags/{tag}', [TagController::class, 'update']);

    // Delete tag
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
});
