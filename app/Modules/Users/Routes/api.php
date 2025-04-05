<?php

use App\Modules\Users\Controllers\Auth\LoginController;
use App\Modules\Users\Controllers\Auth\UserController as AuthUserController;
use App\Modules\Users\Controllers\UserProfileController;
use App\Modules\Users\Controllers\ModulePermissionController;
use App\Modules\Users\Controllers\AuthController;
use App\Modules\Users\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('/')->middleware('auth')->group(function () {
        Route::view('/frontend/{path?}', 'home')->where('path', '.*');
        Route::get('/user', [AuthUserController::class, 'getUser'])->name('user');
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

    Route::prefix('/auth')->group(function () {
        Route::get('/redirect/{platform}', [LoginController::class, 'redirectProvider'])
            ->name('auth.redirect');
        Route::get('/callback/{platform}', [LoginController::class, 'providers'])
            ->name('auth.callback');
    });
});

Route::prefix('v2')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
    Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::prefix('profiles')->group(function () {
            Route::get('', [UserProfileController::class, 'index']);
            Route::post('', [UserProfileController::class, 'store']);
            Route::get('/getActiveProfiles', [UserProfileController::class, 'getUserProfilesActive']);
            Route::get('{id}', [UserProfileController::class, 'show']);
            Route::put('{id}', [UserProfileController::class, 'update']);
            Route::delete('{id}', [UserProfileController::class, 'destroy']);
            Route::post('{id}/syncPermissions', [UserProfileController::class, 'syncPermissions']);
        });

        Route::prefix('module_permissions')->group(function () {
            Route::get('', [ModulePermissionController::class, 'index']);
        });

        Route::get('', [UsersController::class, 'index']);
        Route::get('/list/{role?}', [UsersController::class, 'index']);
        Route::post('', [UsersController::class, 'store']);
        Route::get('{id}', [UsersController::class, 'show']);
        Route::put('{id}', [UsersController::class, 'update']);
        Route::delete('{id}', [UsersController::class, 'destroy']);
        Route::delete('/hard/{id}', [UsersController::class, 'hardDelete']);

        Route::get('total', [UsersController::class, 'total']);
        Route::put('{id}/inactive', [UsersController::class, 'inactive']);
        Route::post('change-password', [UsersController::class, 'changePassword']);
        Route::post('{id}/force-new-password', [UsersController::class, 'forceChangePassword']);
    });
});
