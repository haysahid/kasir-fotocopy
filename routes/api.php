<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SalesController;
use App\Http\Controllers\API\StoreConfigController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);

Route::apiResource('setting', 'SettingController')->middleware('auth:sanctum', ['only' => ['store', 'update', 'destroy']]);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'fetch']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/auth/logout', [UserController::class, 'logout']);

    Route::apiResource('store', StoreController::class);
    Route::put('store/{id}/activate', [StoreController::class, 'activate']);
    Route::put('store/{id}/reject', [StoreController::class, 'reject']);
    Route::put('store/{id}/disable', [StoreController::class, 'disable']);
    Route::put('store/{id}/enable', [StoreController::class, 'enable']);

    Route::apiResource('store-config', StoreConfigController::class);

    Route::apiResource('product', ProductController::class);
    Route::put('product/{id}/disable', [ProductController::class, 'disable']);
    Route::put('product/{id}/enable', [ProductController::class, 'enable']);

    Route::apiResource('purchase', PurchaseController::class);
    Route::apiResource('sales', SalesController::class);

    Route::apiResource('product/{product_id}/image', 'ProductImageController');
});
