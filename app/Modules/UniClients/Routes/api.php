<?php

use Illuminate\Support\Facades\Route;
use App\Modules\UniClients\Controllers\UniClientsController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('request-notification-list', [UniClientsController::class, 'getNotificationChangeReferrerList']);
    Route::get('check-email/{email}', [UniClientsController::class, 'checkEmail']);
    Route::get('{id}', [UniClientsController::class, 'show']);
    Route::get('{id}/business-open', [UniClientsController::class, 'showOnlyBusinessOpen']);
    Route::get('', [UniClientsController::class, 'list']);

    Route::post('', [UniClientsController::class, 'store']);

    Route::put('request-change-referrer/{id}', [UniClientsController::class, 'requestChangeReferrer']);
    Route::put('request-accepted/{requestId}/notification/{notificationId}', [UniClientsController::class, 'requestAccepted']);
    Route::put('request-rejected/{requestId}/notification/{notificationId}', [UniClientsController::class, 'requestRejected']);
    Route::put('delete/{id}', [UniClientsController::class, 'delete']);
    Route::put('{id}', [UniClientsController::class, 'edit']);
    Route::delete('{id}', [UniClientsController::class, 'hardDelete']);
});
