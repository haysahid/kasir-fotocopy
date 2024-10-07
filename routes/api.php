<?php

use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\UserController;
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
    Route::put('store/{id}/disable', [StoreController::class, 'disable']);
    Route::put('store/{id}/enable', [StoreController::class, 'enable']);

    Route::apiResource('product', 'ProductController');
    Route::apiResource('product/{product_id}/image', 'ProductImageController');
    Route::apiResource('sales', 'SalesController');
});
