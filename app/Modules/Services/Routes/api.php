<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Services\Controllers\ServiceController;
use App\Modules\Services\Controllers\ServiceStateController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('states')->group(function () {
        Route::get('', [ServiceStateController::class, 'index'])->name('states.index');
        Route::post('', [ServiceStateController::class, 'store'])->name('states.store');
        Route::get('{id}', [ServiceStateController::class, 'show'])->name('states.show');
        Route::put('{id}/updateDefault', [ServiceStateController::class, 'updateDefault'])->name('bookings.updateDefault');
        Route::put('{id}', [ServiceStateController::class, 'update'])->name('states.update');
        Route::delete('{id}', [ServiceStateController::class, 'destroy'])->name('states.destroy');
    });

    Route::get('colors', [ServiceController::class, 'colors'])->name('services.colors');

    Route::get('', [ServiceController::class, 'index'])->name('services.index');
    Route::post('', [ServiceController::class, 'store'])->name('services.store');
    Route::get('{id}', [ServiceController::class, 'show'])->name('services.show');
    Route::put('{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('{id}', [ServiceController::class, 'destroy'])->name('services.destroy');
});
