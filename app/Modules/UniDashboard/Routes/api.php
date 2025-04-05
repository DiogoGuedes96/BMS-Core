<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\UniDashboard\Controllers\UniDashboardController;

Route::prefix('v1')->middleware(['auth:sanctum', 'cors'])->group(function () {
    Route::get('numbers', [UniDashboardController::class, 'getBoardNumbers']);
    Route::get('toReceive', [UniDashboardController::class, 'getToReceiveBusiness']);
    Route::get('newBusiness', [UniDashboardController::class, 'getNewBusinessByType']);
    Route::get('historyByKanban/{kanbanId}', [UniDashboardController::class, 'getHistoryByKanban']);
});
