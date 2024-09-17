<?php

use Illuminate\Support\Facades\Route;
use Webkul\ZAddArtist\Http\Controllers\Admin\ZAddArtistController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/zaddartist'], function () {
    Route::controller(ZAddArtistController::class)->group(function () {
        Route::get('', 'index')->name('admin.zaddartist.index');
        Route::get('create', 'create')->name('admin.zaddartist.create');
        Route::post('create', 'store');
    });
});