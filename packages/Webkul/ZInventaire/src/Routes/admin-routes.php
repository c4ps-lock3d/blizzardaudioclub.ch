<?php

use Illuminate\Support\Facades\Route;
use Webkul\ZInventaire\Http\Controllers\Admin\ZInventaireController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/zinventaire'], function () {
    Route::controller(ZInventaireController::class)->group(function () {
        Route::get('', 'index')->name('admin.zinventaire.index');
    });
});