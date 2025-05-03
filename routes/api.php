<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SalesController;
use App\Http\Controllers\API\StoreConfigController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\OptionController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\ProductImageController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\SubscribeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\API\CheckActiveUser;
use App\Http\Middleware\API\CheckAdminRole;
use App\Http\Middleware\API\CheckUserRoleAndStore;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);
Route::get('setting', [SettingController::class, 'index']);

Route::post('report-pdf', [ReportController::class, 'generatePdf']);

Route::middleware(['auth:sanctum', CheckActiveUser::class])->group(function () {
    Route::get('/profile', [UserController::class, 'fetch']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/auth/logout', [UserController::class, 'logout']);

    Route::apiResource('store', StoreController::class);
    Route::put('store/{id}/activate', [StoreController::class, 'activate']);
    Route::put('store/{id}/reject', [StoreController::class, 'reject']);
    Route::put('store/{id}/disable', [StoreController::class, 'disable']);
    Route::put('store/{id}/enable', [StoreController::class, 'enable']);

    Route::apiResource('store-config', StoreConfigController::class);

    Route::middleware(CheckUserRoleAndStore::class . ':1,2,4')->group(function () {
        Route::apiResource('employee', EmployeeController::class);
        Route::put('employee/{id}/disable', [EmployeeController::class, 'disable']);
        Route::put('employee/{id}/enable', [EmployeeController::class, 'enable']);

        Route::get('report/purchase', [ReportController::class, 'purchase']);
        Route::get('report/sales', [ReportController::class, 'sales']);
    });

    Route::apiResource('product', ProductController::class);
    Route::put('product/{id}/disable', [ProductController::class, 'disable']);
    Route::put('product/{id}/enable', [ProductController::class, 'enable']);
    Route::get('product-community', [ProductController::class, 'getCommunityProducts']);

    Route::apiResource('product/{product_id}/image', ProductImageController::class);

    Route::apiResource('purchase', PurchaseController::class);
    Route::apiResource('sales', SalesController::class);
    Route::post('sales-receipt', [SalesController::class, 'generateReceipt']);

    Route::post('add-expired-product', [SalesController::class, 'addExpiredProduct']);

    Route::get('store/{id}/summary', [StoreController::class, 'summary']);
    Route::get('store/{id}/graph', [StoreController::class, 'graph']);
    Route::get('store/{id}/low-stock-product', [StoreController::class, 'lowStockProduct']);

    Route::apiResource('role', RoleController::class);
    Route::get('role-dropdown', [RoleController::class, 'dropdown']);

    // Admin
    Route::middleware(CheckAdminRole::class)->group(function () {
        Route::apiResource('user', UserController::class);
        Route::put('user/{id}/disable', [UserController::class, 'disable']);
        Route::put('user/{id}/enable', [UserController::class, 'enable']);

        Route::get('admin/summary', [AdminController::class, 'summary']);

        Route::post('setting', [SettingController::class, 'store']);
        Route::get('setting/{id}', [SettingController::class, 'show']);
        Route::post('setting/{id}', [SettingController::class, 'update']);
        Route::delete('setting/{id}', [SettingController::class, 'destroy']);
    });

    // Subcription
    Route::apiResource('option', OptionController::class);
    Route::get('option-dropdown', [OptionController::class, 'dropdown']);

    Route::get('plan', [PlanController::class, 'index']);
    Route::put('plan/{id}', [PlanController::class, 'update']);
    Route::delete('plan/{id}', [PlanController::class, 'destroy']);
    Route::put('plan/{id}/disable', [PlanController::class, 'disable']);
    Route::put('plan/{id}/enable', [PlanController::class, 'enable']);
    Route::put('plan/{id}/priority/{value}', [PlanController::class, 'setPriority']);

    Route::apiResource('payment-method', PaymentMethodController::class);
    Route::get('payment-method-dropdown', [PaymentMethodController::class, 'dropdown']);
    Route::apiResource('invoice-payment', InvoicePaymentController::class);

    Route::apiResource('invoice', InvoiceController::class);
    Route::get('active-invoice', [InvoiceController::class, 'getActiveInvoice']);

    Route::post('subscribe/create-snap-token', [SubscribeController::class, 'createSnapToken']);

    Route::apiResource('subscription', SubscriptionController::class);
    Route::get('subscription-history', [SubscriptionController::class, 'history']);
});

Route::get('plan/{id}', [PlanController::class, 'show']);
Route::get('plan-dropdown', [PlanController::class, 'dropdown']);
