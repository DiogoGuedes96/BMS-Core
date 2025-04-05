<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Vehicles\Controllers\VehicleController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('{id}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::put('{id}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('{id}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
});