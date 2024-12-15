<?php

use App\Http\Controllers\Api\{UserController,ArticleController,NewsController,UserPreferencesController};
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/send-password-link', [UserController::class, 'sendPasswordLink']);
Route::post('/password-reset', [UserController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::resource('articles', ArticleController::class);
    
    Route::post('/preferences', [UserPreferencesController::class, 'setPreferences']);
    Route::get('/preferences', [UserPreferencesController::class, 'getPreferences']);
    Route::get('/personalized-feed', [UserPreferencesController::class, 'getPersonalizedFeed']);

});

Route::resource('news', NewsController::class);

// Route::get('/articles', [ArticleController::class, 'index']);
// Route::get('/articles/{article}', [ArticleController::class, 'show']);