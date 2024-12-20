<?php

use Illuminate\Support\Facades\Route;
use Webkul\ZInventaire\Http\Controllers\Shop\ZInventaireController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'zinventaire'], function () {
    Route::get('', [ZInventaireController::class, 'index'])->name('shop.zinventaire.index');
});