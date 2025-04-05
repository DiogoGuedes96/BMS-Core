<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Bookings\Controllers\BookingController;
use App\Modules\Bookings\Controllers\ClientController;
use App\Modules\Bookings\Controllers\ServiceController;
use App\Modules\Bookings\Controllers\ReportController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('reports')->group(function () {
        Route::get('workers', [ReportController::class, 'workers'])->name('reports.workers');
        Route::get('workers/{id}', [ReportController::class, 'showWorker'])->name('reports.workers.show');
    });

    Route::prefix('services')->group(function () {
        Route::get('columns', [ServiceController::class, 'columns'])->name('bookingServices.columns');
        Route::put('updateColumns', [ServiceController::class, 'updateColumns'])->name('bookingServices.updateColumns');
        Route::get('trashed', [ServiceController::class, 'indexTrashed'])->name('bookingServices.indexTrashed');
        Route::get('', [ServiceController::class, 'index'])->name('bookingServices.index');
        Route::post('', [ServiceController::class, 'store'])->name('bookingServices.store');
        Route::post('sendTimetable', [ServiceController::class, 'sendTimetable'])->name('bookings.sendTimetable');
        Route::get('{id}', [ServiceController::class, 'show'])->name('bookingServices.show');
        Route::put('{id}', [ServiceController::class, 'update'])->name('bookingServices.update');
        Route::delete('{id}', [ServiceController::class, 'destroy'])->name('bookingServices.destroy');
    });

    Route::prefix('clients')->group(function () {
        Route::get('', [ClientController::class, 'index'])->name('bookingClients.index');
        Route::post('', [ClientController::class, 'store'])->name('bookingClients.store');
        Route::get('{id}', [ClientController::class, 'show'])->name('bookingClients.show');
        Route::put('{id}', [ClientController::class, 'update'])->name('bookingClients.update');
        Route::delete('{id}', [ClientController::class, 'destroy'])->name('bookingClients.destroy');
    });

    Route::get('', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('trashed', [BookingController::class, 'indexTrashed'])->name('bookings.indexTrashed');
    Route::post('draft/{id?}', [BookingController::class, 'draft'])->name('bookings.draft');
    Route::post('pending', [BookingController::class, 'pending'])->name('bookings.pending');
    Route::post('{id}', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('{id}/sendVoucher', [BookingController::class, 'sendVoucher'])->name('bookings.sendVoucher');
    Route::get('{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('{id}/trashed', [BookingController::class, 'showTrashed'])->name('bookings.showTrashed');
    Route::put('{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::put('{id}/updateStatus', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::put('{id}/updateVoucher', [BookingController::class, 'updateVoucher'])->name('bookings.updateVoucher');
    Route::put('{id}/updatePending', [BookingController::class, 'updatePending'])->name('bookings.updatePending');
    Route::put('{id}/restore', [BookingController::class, 'restore'])->name('bookings.restore');
    Route::delete('force', [BookingController::class, 'forceDestroy'])->name('bookings.forceDestroy');
    Route::delete('{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
});
