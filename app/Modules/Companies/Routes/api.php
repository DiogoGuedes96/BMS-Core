<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Companies\Controllers\CompaniesController;

Route::prefix('v1')->group(function () {
    Route::get('details', [CompaniesController::class, 'show']);
    Route::post('', [CompaniesController::class, 'store']);
    Route::put('', [CompaniesController::class, 'update']);
});
