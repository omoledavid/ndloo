<?php

use App\Http\Controllers\AbuseController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoostController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\Admin\ManageUsersController;
use App\Http\Controllers\Admin\BoostController as AdminBoostController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/countries', 'getCountries');
    Route::post('/register', 'allSignup');
    Route::post('/details/register', 'detailSignup');
    Route::post('/password/register', 'passwordSignup');
    Route::post('/login', 'login');
    Route::post('/otp/login', 'otpLogin');
    Route::post('/otp/verify', 'verifyOtp');
    Route::post('/email/verify', 'verifyCode');
});
//Admin
Route::controller(ManageUsersController::class)->group(function () {
    Route::get('/all-users', 'allUsers');
    Route::get('/user/{user}', 'viewUser');
    Route::get('/banned/user/{user}', 'bannedUser');
});

Route::controller(AdminBoostController::class)->group(function () {
    Route::get('/boost', 'boost');
    Route::post('/boost', 'createBoost');
    Route::get('/boost/{boostPlan}', 'viewBoostPlan');
    Route::get('/boost/edit/{plan}', 'viewUser');
    Route::get('/banned/user/{user}', 'bannedUser');
});

Route::controller(PasswordResetController::class)->group(function () {
    Route::post('/account/recover', 'sendCode');
    Route::post('/account/recover/code', 'verifyCode');
    Route::post('/account/password/reset', 'changePassword');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(BoostController::class)->group(function () {
        Route::get('/boost/plans', 'plans');
        Route::post('/profile/boost/{plan}', 'boost');
    });

    Route::controller(ChatController::class)->prefix('messages')->group(function () {
        Route::get('/', 'recentMessages');
        Route::post('/send/{recipient}', 'sendMessage');
        Route::get('/{user}/chats', 'chatMessages');
        Route::get('/{user}/mark', 'markRead');
    });

    Route::controller(GiftController::class)->group(function () {
        Route::get('/gift/plans', 'plans');
        Route::get('/gift/my/plans', 'myPlans');
        Route::post('/gift/{plan}/purchase/{recipient}', 'purchase');
        Route::post('/gift/{gift}/redeem', 'redeemGift');
    });

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('/{user}', 'profile');
        Route::post('/update', 'updateProfile');
        Route::post('/images/upload', 'uploadImages');
        Route::delete('/images/{image}/remove', 'removeImage');
    });

    Route::controller(ReactionController::class)->prefix('actions')->group(function () {
        Route::get('/reactions', 'reactions');
        Route::get('/reactions/others', 'reactionsToMe');
        Route::get('/toggle/{recipient}', 'toggleReaction');
    });

    Route::controller(SubscriptionController::class)->prefix('subscriptions')->group(function () {
        Route::get('/plans', 'plans');
        Route::post('/{plan}/subscribe', 'subscribe');
        Route::post('/{plan}/unsubscribe', 'unsubscribe');
    });

    Route::controller(DepositController::class)->prefix('deposit')->group(function () {
        Route::post('/options', 'getOptions');
        Route::post('/rate', 'getRate');
        Route::post('/payment/info', 'generateInfo');
        Route::get('/payment/verify/{payment:reference}', 'verifyPayment');
    });
    Route::get('paystack/payment/callback', [PaymentController::class, 'callback'])->name('paystack.callback');


    Route::controller(AccountController::class)->prefix('account')->group(function () {
        Route::post('/language/change', 'changeLanguage');
        Route::post('/email/change', 'changeEmail');
        Route::post('/password/change', 'changePassword');
        Route::get('/notifications/toggle', 'toggleNotifications');
        Route::get('/transactions', 'getTransactions');
        Route::delete('/delete', 'deleteAccount');
    });

    Route::controller(WithdrawalController::class)->prefix('withdrawal')->group(function () {
        Route::get('/countries', 'countries');
        Route::post('/withdraw', 'withdraw');
        Route::get('/verify', 'verify');
    });

    Route::get('/matches', [MatchController::class, 'matches']);
    Route::get('/currencies', [CurrencyController::class, 'currencies']);
    Route::post('/abuse/{account}/report', [AbuseController::class, 'report']);
});
