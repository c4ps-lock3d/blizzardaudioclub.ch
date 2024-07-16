<?php

use Illuminate\Support\Facades\Route;
use Webkul\RateRules\Http\Controllers\Admin\RateRulesController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/configuration/sales/carriers'], function () {
    Route::controller(RateRulesController::class)->group(function () {
        Route::get('', 'index')->name('admin.configuration.sales.carriers.index');
    });
});