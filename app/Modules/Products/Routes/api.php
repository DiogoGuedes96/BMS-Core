<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Products\Controllers\ProductsController;

Route::prefix('v1')->group(function () {
    Route::post('', [ProductsController::class, 'store']);
    Route::get('', [ProductsController::class, 'list']);
    Route::get('all', [ProductsController::class, 'listAll']);
    Route::get('{id}', [ProductsController::class, 'show']);
    Route::put('{id}', [ProductsController::class, 'edit']);
    Route::put('delete/{id}', [ProductsController::class, 'delete']);
});
