<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('', [NotificationController::class, 'list']);
    Route::patch('mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('request-by-user/', [NotificationController::class, 'requestedByUser']);
});
