<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PreferenceController;

Route::middleware(['throttle:10,1'])->post('register', [AuthController::class, 'register']);
Route::middleware(['throttle:10,1'])->post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);

    Route::post('preferences', [PreferenceController::class, 'store']);
    Route::get('preferences', [PreferenceController::class, 'show']);
    Route::get('/user/news-feed', [ArticleController::class, 'personalizedFeed']);
});