<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Primavera\Controllers\PrimaveraController;

Route::prefix('v1')->group(function () {
    Route::get('example', [PrimaveraController::class, 'example']);
});
