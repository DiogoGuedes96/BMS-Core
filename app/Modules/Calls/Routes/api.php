<?php

use App\Modules\Calls\Controllers\AsteriskController;
use App\Modules\Calls\Controllers\CallsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsteriskController2;

//TODO Route Only for testing
//TODO Remove Later
Route::prefix('v2')->group(function () {
    Route::get('/test/clearPhones', [CallsController::class, 'clearPhones']);
});

Route::prefix('v2')->middleware('auth:sanctum')->group(function () {
    Route::get('/in-progress', [CallsController::class, 'inProgress']);
    Route::get('/hangup', [CallsController::class, 'hangup']);
    Route::get('/hangup/export', [CallsController::class, 'exportCallsHangup']);
    Route::get('/missed', [CallsController::class, 'missed']);
    Route::put('/closeGhostCall/{call}', [CallsController::class, 'closeGhostCall']);
    Route::put('/update', [CallsController::class, 'updateCall']);

    Route::prefix('asterisk')->group(function () {
        Route::post('/view', [AsteriskController::class, 'index']);
        Route::post('/update', [AsteriskController::class, 'update']);
    });

    Route::prefix('cache')->group(function () {
        Route::get('/blocked', [CallsController::class, 'getAllCallsBlocked']);
        Route::post('/add', [CallsController::class, 'setCallBlockedOnCache']);
        Route::put('/remove', [CallsController::class, 'removeCallBlockedFromCache']);
        Route::put('/remove/all', [CallsController::class, 'removeAllCallsBlockedFromCache']);
    });

    Route::get('/{callId}', [CallsController::class, 'getOne']);
});
