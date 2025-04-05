<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Veiculos\Controllers\VeiculosController;

Route::prefix('v1')->group(function () {
    Route::get('example', [VeiculosController::class, 'example']);
});
