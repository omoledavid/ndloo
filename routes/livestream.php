<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Livestream\LivestreamController;
use App\Http\Controllers\Livestream\CommentController;
use App\Http\Controllers\Livestream\GiftController;
use App\Http\Controllers\Livestream\CategoryController;
use App\Http\Controllers\Livestream\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::get('/livestreams', [LivestreamController::class, 'index'])->name('livestreams.index');
Route::get('/livestreams/{id}', [LivestreamController::class, 'show'])->name('livestreams.show');
Route::get('/livestreams/{id}/comments', [CommentController::class, 'index'])->name('livestreams.comments.index');
Route::get('/livestreams/{id}/gifts', [GiftController::class, 'getGiftTransactions'])->name('livestreams.gifts.index');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{id}/livestreams', [CategoryController::class, 'getLivestreams']);

Route::get('/gifts', [GiftController::class, 'index']);

Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/users/{id}/livestreams', [UserController::class, 'getLivestreams']);
Route::get('/users/{id}/current-livestream', [UserController::class, 'getCurrentLivestream']);
Route::get('/users/{id}/followers', [UserController::class, 'getFollowers']);
Route::get('/users/{id}/following', [UserController::class, 'getFollowing']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Livestream routes
    Route::post('/livestreams', [LivestreamController::class, 'store'])->name('livestreams.store');
    Route::put('/livestreams/{id}', [LivestreamController::class, 'update'])->name('livestreams.update');
    Route::delete('/livestreams/{id}', [LivestreamController::class, 'destroy'])->name('livestreams.destroy');
    Route::post('/livestreams/{id}/start', [LivestreamController::class, 'startStream'])->name('livestreams.start');
    Route::post('/livestreams/{id}/end', [LivestreamController::class, 'endStream'])->name('livestreams.end');
    Route::put('/livestreams/{id}/viewer-count', [LivestreamController::class, 'updateViewerCount'])->name('livestreams.updateViewerCount');
    
    // Comment routes
    Route::post('/livestreams/{livestreamId}/comments', [CommentController::class, 'store']);
    Route::delete('/livestreams/{livestreamId}/comments/{commentId}', [CommentController::class, 'destroy']);
    
    // Gift routes
    Route::post('/livestreams/{livestreamId}/gifts', [GiftController::class, 'sendGift']);
    Route::post('livestreams/gifts/send/{livestreamId}', [GiftController::class, 'sendGift'])->name('gifts.send');
    
    // Follow routes
    Route::post('/users/{id}/follow', [UserController::class, 'follow']);
    Route::delete('/users/{id}/unfollow', [UserController::class, 'unfollow']);
});