<?php

use Illuminate\Support\Facades\Route;
use Webkul\RateRules\Http\Controllers\Shop\RateRulesController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'raterules'], function () {
    Route::get('', [RateRulesController::class, 'index'])->name('shop.raterules.index');
});