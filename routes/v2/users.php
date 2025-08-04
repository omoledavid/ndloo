<?php

use App\Http\Controllers\v2\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {

        // Subscription
        Route::post('/subscriptions/subscribe', [SubscriptionController::class, 'subscribe']);
        Route::get('/subscriptions/activesubscription', [SubscriptionController::class, 'subscription']);
        Route::post('/subscriptions/unsubscribe', [SubscriptionController::class, 'unsubscribe']);
        Route::post('/subscriptions/can-use-feature', [SubscriptionController::class, 'canUseFeature']);
    });
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans']);
    Route::get('/subscriptions/features', [SubscriptionController::class, 'features']);
});

