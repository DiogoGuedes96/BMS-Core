<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Tables\Controllers\TableController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('', [TableController::class, 'index'])->name('tables.index');
    Route::post('', [TableController::class, 'store'])->name('tables.store');
    Route::get('{id}', [TableController::class, 'show'])->name('tables.show');
    Route::put('{id}', [TableController::class, 'update'])->name('tables.update');
    Route::delete('{id}', [TableController::class, 'destroy'])->name('tables.destroy');
    Route::post('{id}/routes', [TableController::class, 'syncRoutes'])->name('tables.syncRoutes');
});
