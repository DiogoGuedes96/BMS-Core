<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Patients\Controllers\PatientsController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('store', [PatientsController::class, 'newPatient']);
    Route::get('total', [PatientsController::class, 'total']);
    Route::get('list', [PatientsController::class, 'listAllPatients']);
    Route::put('edit/{patient}', [PatientsController::class, 'editPatient']);
    Route::delete('delete/{patient}', [PatientsController::class, 'deletePatient']);
    Route::post('/responsible/store', [PatientsController::class, 'addResponsible']);
    Route::get('history/{patient}', [PatientsController::class, 'patientSchedulingHistory']);
    Route::get('future/{patient}', [PatientsController::class, 'patientFutureScheduling']);
    Route::get('/patients-from-responsible/{responsible_id}', [PatientsController::class, 'getPatientsFromResponsible']);
    Route::get('/patients-from-client/{client_id}', [PatientsController::class, 'getPatientsFromClients']);
    Route::get('/details/{patient_id}', [PatientsController::class, 'getPatientDetails']);
});
