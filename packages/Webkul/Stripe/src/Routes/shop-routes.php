<?php

use Illuminate\Support\Facades\Route;
use Webkul\Stripe\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {

    /**
     * Stripe payment routes
     */

    Route::get('/stripe-redirect-twint', [PaymentController::class, 'redirecttwint'])->name('stripetwint.process');
    Route::get('/stripe-redirect-paypal', [PaymentController::class, 'redirectpaypal'])->name('stripepaypal.process');
    Route::get('/stripe-redirect', [PaymentController::class, 'redirectcard'])->name('stripe.process');
    Route::get('/stripe-success', [PaymentController::class, 'success'])->name('stripe.success');
    Route::get('/stripe-cancel', [PaymentController::class, 'failure'])->name('stripe.cancel');
});
