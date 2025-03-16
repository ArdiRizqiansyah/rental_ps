<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransHandlerController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::post('/webhook/midtrans/handler', [MidtransHandlerController::class, 'callback']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout/{code}/store', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/fee-check', [CheckoutController::class, 'feeCheck'])->name('checkout.fee.check');

        Route::get('/payment/{code}', [PaymentController::class, 'index'])->name('payment.index');
    });
});