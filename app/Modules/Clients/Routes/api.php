<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Clients\Controllers\ClientsController;

Route::prefix('v2')->middleware('auth:sanctum')->group(function () {
    Route::get('/all', [ClientsController::class, 'listAllClients']);
    Route::get('/clients-from-responsible/{responsible_id}', [ClientsController::class, 'getClientsFromResponsible']);
    Route::get('/total', [ClientsController::class, 'total']);
    Route::post('/create', [ClientsController::class, 'createClient']);
    Route::put('/edit', [ClientsController::class, 'editClient']);
    Route::delete('/delete/{clients_id}', [ClientsController::class, 'softDeleteClient']);
});
