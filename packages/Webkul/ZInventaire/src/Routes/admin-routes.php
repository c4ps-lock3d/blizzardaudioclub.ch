<?php

use Illuminate\Support\Facades\Route;
use Webkul\ZInventaire\Http\Controllers\Admin\ZInventaireController;
use Webkul\Store\Http\Controllers\API\ProductController;
use Webkul\Admin\Http\Controllers\Catalog\ProductController as AdminProductController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => 'admin/zinventaire'], function () {
    Route::controller(ZInventaireController::class)->group(function () {
        Route::get('', 'index')->name('admin.zinventaire.index');
    });
    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('inv', 'inventaire');
    });
    Route::controller(AdminProductController::class)->prefix('products')->group(function () {
        Route::put('edit/{id}', 'update')->name('admin.catalog.products.update');
    });
});

