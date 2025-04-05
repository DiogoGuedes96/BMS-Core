<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Workers\Controllers\WorkerController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('', [WorkerController::class, 'index'])->name('workers.index');
    Route::post('', [WorkerController::class, 'store'])->name('workers.store');
    Route::get('{id}', [WorkerController::class, 'show'])->name('workers.show');
    Route::put('{id}', [WorkerController::class, 'update'])->name('workers.update');
    Route::delete('{id}', [WorkerController::class, 'destroy'])->name('workers.destroy');
});