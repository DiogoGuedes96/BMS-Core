<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\Controllers\DashboardController;

Route::prefix('v2')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/{entity}/kpis', [DashboardController::class, 'getKpis'])
            ->middleware('module.permission:homepage,calls,order,orders-tracking,scheduling,products');

        Route::get('kpis', [DashboardController::class, 'kpis'])->name('dashboard.kpis');
    });
});
