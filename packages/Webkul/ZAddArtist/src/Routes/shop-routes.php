<?php

use Illuminate\Support\Facades\Route;
use Webkul\ZAddArtist\Http\Controllers\Shop\ZAddArtistController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'zaddartist'], function () {
    Route::get('', [ZAddArtistController::class, 'index'])->name('shop.zaddartist.index');
});