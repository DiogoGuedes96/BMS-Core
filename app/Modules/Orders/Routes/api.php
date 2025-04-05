<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Orders\Controllers\OrdersController;

Route::prefix('v1')->group(function () {
    Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
        Route::get('/all', [OrdersController::class, 'getAllOrders'])->middleware('module.permission:order,orders-tracking');
        Route::get('/{order_id}', [OrdersController::class, 'getOrderById'])->middleware('module.permission:order,orders-tracking');
        Route::get('/products/{clientId}',  [OrdersController::class, 'getProductsByOrders'])->middleware('module.permission:order');
        Route::post('/client/mostBought/{bms_client}', [OrdersController::class, 'getProductsMostBoughtProductsByClient'])->middleware('module.permission:order');
        Route::post('/client/lessBought/{bms_client}', [OrdersController::class, 'getLessBoughtProductsByClient'])->middleware('module.permission:order');
        Route::post('/saveNewOrder', [OrdersController::class, 'store'])->middleware('module.permission:order');
        Route::post('/setPriorityOrder/{order_id}', [OrdersController::class, 'setPriorityOrder'])->middleware('module.permission:order');
        Route::post('/update-order/{id}', [OrdersController::class, 'update'])->middleware('module.permission:order,orders-tracking');
        Route::post('/update/{id}', [OrdersController::class, 'updateOrderAndAddProducts'])->middleware('module.permission:order,orders-tracking');
        Route::post('/forkOrder', [OrdersController::class, 'forkOrder'])->middleware('module.permission:orders-tracking');
        Route::post('/removeProducts', [OrdersController::class, 'removeProductsFromOrder'])->middleware('module.permission:orders-tracking');
        Route::put('/{order}/setStatus/{status}', [OrdersController::class, 'setOrderStatus'])->middleware('module.permission:order,orders-tracking');
        Route::get('/getOrdersByStatus/{status}', [OrdersController::class, 'getOrdersByStatus'])->middleware('module.permission:orders-tracking');
        Route::get('/searchOrdersByInput/{input}', [OrdersController::class, 'searchOrdersByInput'])->middleware('module.permission:orders-tracking');
        Route::put('/{order}/pending', [OrdersController::class, 'updateOrderPending'])->middleware('module.permission:orders-tracking');
        Route::get('/invoice/{order}', [OrdersController::class, 'generatePDFOrder'])->middleware('module.permission:orders-tracking');
        Route::put('/validateStock/{order}', [OrdersController::class, 'validateStock'])->middleware('module.permission:order,orders-tracking');
    });
});
