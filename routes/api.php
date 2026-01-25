<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CollectionController;

// Public routes 
Route::post('/signup', [UserController::class, 'signup']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes 
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', [UserController::class, 'getCurrentUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::delete('/profile', [UserController::class, 'deleteAccount']);
    
    // Bookmark routes 
    Route::get('/bookmarks/favorites', [BookmarkController::class, 'favorites']);
    Route::apiResource('bookmarks', BookmarkController::class);
    Route::post('/bookmarks/{bookmark}/favorite', [BookmarkController::class, 'favorite']);
    Route::post('/bookmarks/{bookmark}/unfavorite', [BookmarkController::class, 'unfavorite']);
    Route::post('/bookmarks/{bookmark}/toggle-favorite', [BookmarkController::class, 'toggleFavorite']);
    
    // Collection routes
    Route::apiResource('collections', CollectionController::class);
    Route::post('/collections/{collection}/bookmarks/{bookmark}', [CollectionController::class, 'addBookmark']);
    Route::delete('/collections/{collection}/bookmarks/{bookmark}', [CollectionController::class, 'removeBookmark']);
    
    // Tag routes
    Route::get('/tags', [TagController::class, 'index']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
});