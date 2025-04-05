<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\AmbulanceCrew\Controllers\AmbulanceCrewController;
use App\Modules\AmbulanceCrew\Controllers\AmbulanceGroupController;

Route::prefix('v1')->group(function () {
    Route::get('', [AmbulanceCrewController::class, 'listAllAmbulanceCrew']);
    Route::post('store', [AmbulanceCrewController::class, 'newAmbulanceCrew']);
    Route::put('edit/{ambulanceCrew}', [AmbulanceCrewController::class, 'editAmbulanceCrew']);
    Route::delete('delete/{ambulanceCrew}', [AmbulanceCrewController::class, 'delAmbulanceCrew']);
    Route::prefix('group')->group(function () {
        Route::get('', [AmbulanceGroupController::class, 'listAllAmbulanceGroup']);
        Route::post('store', [AmbulanceGroupController::class, 'newAmbulanceGroup']);
        Route::put('edit/{ambulanceGroup}', [AmbulanceGroupController::class, 'editAmbulanceGroup']);
        Route::delete('delete/{ambulanceGroup}', [AmbulanceGroupController::class, 'delAmbulanceGroup']);
    }); 
});
