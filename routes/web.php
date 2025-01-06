<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/payment/{channel}', function () {
    return view('payment');
});
Route::get('/', function (){
    return response()->json([
        'Ndloo api development in progress'
    ]);
});
Route::get('/login', function () {
    return response()->json([
        'Login'
    ]);
})->name('login');
