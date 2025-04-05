<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Routes\Controllers\RoutesController;
use App\Modules\Routes\Controllers\ZonesController;
use App\Modules\Routes\Controllers\LocationController;

Route::prefix('v1')->group(function () {
    Route::prefix('zone')->group(function () {
        Route::get('all', [ZonesController::class, 'all']);
        Route::get('', [ZonesController::class, 'listAllPaged']);
        Route::get('{id}', [ZonesController::class, 'one']);
        Route::post('', [ZonesController::class, 'save']);
        Route::put('{id}', [ZonesController::class, 'edit']);
        Route::delete('{id}', [ZonesController::class, 'delete']);
    });

    Route::prefix('locations')->group(function () {
        Route::get('', [LocationController::class, 'index'])->name('locations.index');
        Route::post('', [LocationController::class, 'store'])->name('locations.store');
        Route::get('{id}', [LocationController::class, 'show'])->name('locations.show');
        Route::put('{id}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
    });

    Route::get('', [RoutesController::class, 'list']);
    Route::get('{id}', [RoutesController::class, 'one']);
    Route::post('', [RoutesController::class, 'save']);
    Route::put('{id}', [RoutesController::class, 'edit']);
    Route::delete('{id}', [RoutesController::class, 'delete']);
});
