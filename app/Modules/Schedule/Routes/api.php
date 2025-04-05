<?php

use App\Modules\Schedule\Controllers\BmsScheduleEventController;
use App\Modules\Schedule\Controllers\BmsScheduleEventRememberController;
use App\Modules\Schedule\Controllers\BmsScheduleEventReminderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::prefix('event')->middleware('auth:sanctum')->group(function () {
        Route::prefix('reminder')->group(function () {
            Route::post('create', [BmsScheduleEventReminderController::class, 'createEventReminder']);
            Route::put('edit', [BmsScheduleEventReminderController::class, 'editEventReminder']);
            Route::delete('delete/{eventId}/{type}', [BmsScheduleEventReminderController::class, 'softDeleteBmsScheduleEvent']);
        });
        Route::prefix('remember')->group(function () {
            Route::put('done/{eventId}', [BmsScheduleEventRememberController::class, 'setDone']);
            Route::get('listCurrentMinute/{onlyRead?}', [BmsScheduleEventRememberController::class, 'listCurrentMinuteBmsScheduleEventsRemembers']);
            Route::post('delay', [BmsScheduleEventController::class, 'delayBmsScheduleEvent']);
        });
        Route::post('listByDates', [BmsScheduleEventController::class, 'listEventsByDatesFromUser']);
    });
});
