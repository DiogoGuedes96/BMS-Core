<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Feedback\Controllers\FeedbackController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('list', [FeedbackController::class, 'listAllFeedbacks']);
    Route::post('store',[FeedbackController::class, 'newFeedback']);
    Route::put('edit/{feedback}', [FeedbackController::class, 'editFeedback']);
    Route::delete('delete/{feedback}', [FeedbackController::class, 'deletedFeedback']);
});
