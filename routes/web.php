<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/payment/{channel}', function () {
    return view('payment');
});
Route::get('login', function (){
    return response()->json([
        'message' => 'You are logged in'
    ])->name('login');
});
