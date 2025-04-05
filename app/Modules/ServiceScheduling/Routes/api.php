<?php

use App\Modules\ServiceScheduling\Controllers\ServiceSchedulingCanceledController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\ServiceScheduling\Controllers\ServiceSchedulingController;
use App\Modules\ServiceScheduling\Models\ServiceSchedulingModel;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('', [ServiceSchedulingController::class, 'list']);
    Route::get('schedules-from-patient/{patientId}/{withoutReturns?}', [ServiceSchedulingController::class, 'getSchedulesFromPatient']);
    Route::get('total', [ServiceSchedulingController::class, 'total']);
    Route::post('store', [ServiceSchedulingController::class, 'newSchedule']);
    Route::post('delete', [ServiceSchedulingController::class, 'deleteSchedule']);
    Route::put('edit/{schedule}', [ServiceSchedulingController::class, 'editSchedule']);
    Route::get('getRepeatSchedulePosition/{schedule}', [ServiceSchedulingController::class, 'getRepeatSchedulePosition']);
    Route::prefix('/canceled')->group(function () {
        Route::get('/', [ServiceSchedulingCanceledController::class, 'list']);
        Route::get('{schedule}', [ServiceSchedulingCanceledController::class, 'getSchedulingByItem']);
        Route::put('restore/{schedule}', [ServiceSchedulingController::class, 'restoreCanceledSchedule'])->withTrashed();
    });
});