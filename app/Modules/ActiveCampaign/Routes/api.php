<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\ActiveCampaign\Controllers\ActiveCampaignController;

Route::prefix('v1')->group(function () {
    Route::get('example', [ActiveCampaignController::class, 'example']);
});
