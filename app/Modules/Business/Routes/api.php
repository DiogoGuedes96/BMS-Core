<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Business\Controllers\BusinessController;
use App\Modules\Business\Controllers\BusinessFollowupController;
use App\Modules\Business\Controllers\BusinessKanbanController;
use App\Modules\Business\Controllers\BusinessNotesController;
use App\Modules\Business\Controllers\BusinessPaymentController;

Route::prefix('v1')->middleware(['auth:sanctum', 'cors'])->group(function () {
    Route::prefix('kanban')->group(function () {
        Route::post('store', [BusinessKanbanController::class, 'create']);
        Route::get('', [BusinessKanbanController::class, 'list']);
        Route::put('move', [BusinessKanbanController::class, 'move']);
        Route::put('moveColumns', [BusinessKanbanController::class, 'moveColumns']);
        Route::get('types', [BusinessKanbanController::class, 'listTypes']);
        Route::get('{type}', [BusinessKanbanController::class, 'listOne']);
        Route::put('{id}', [BusinessKanbanController::class, 'update']);
        Route::delete('{id}', [BusinessKanbanController::class, 'delete']);
    });

    Route::prefix('notes')->group(function () {
        Route::post('store', [BusinessNotesController::class, 'create']);
        Route::get('{businessId}', [BusinessNotesController::class, 'list']);
        Route::delete('{id}', [BusinessNotesController::class, 'delete']);
    });

    Route::prefix('followup')->group(function () {
        Route::post('store', [BusinessFollowupController::class, 'create']);
        Route::get('{id}/completed', [BusinessFollowupController::class, 'markAsCompleted']);
        Route::get('{businessId}', [BusinessFollowupController::class, 'list']);
        Route::delete('{id}', [BusinessFollowupController::class, 'delete']);
        Route::put('{id}', [BusinessFollowupController::class, 'update']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('historic/group', [BusinessPaymentController::class, 'listGroupPaymentHistoric']);
        Route::get('historic', [BusinessPaymentController::class, 'listPaymentsHistoric']);
        Route::get('show', [BusinessPaymentController::class, 'details']);
        // Route::get('list', [BusinessPaymentController::class, 'show']);
        Route::get('by/{userId}', [BusinessPaymentController::class, 'paymentByUser']);
        //===========================
        Route::get('list', [BusinessPaymentController::class, 'listPayments']);
        Route::get('list/responsibles/{id}', [BusinessPaymentController::class, 'listPaymentResponsible']);
        Route::put('made/{id}', [BusinessPaymentController::class, 'made']);
        Route::patch('generate', [BusinessPaymentController::class, 'generate']);
    });

    Route::get('', [BusinessController::class, 'list']);
    Route::post('', [BusinessController::class, 'store']);
    Route::put('state/{id}', [BusinessController::class, 'updateState']);
    Route::put('{id}', [BusinessController::class, 'update']);
    Route::put('{id}/client/{clientId}', [BusinessController::class, 'updateBusinessClient']);
    Route::delete('{id}', [BusinessController::class, 'delete']);
    Route::get('{id}/cancel', [BusinessController::class, 'cancelOneBusiness']);
    Route::get('{id}/close', [BusinessController::class, 'closeOneBusiness']);
    Route::get('{id}/reopen', [BusinessController::class, 'reopenOneBusiness']);
    Route::get('{id}', [BusinessController::class, 'show']);
});
