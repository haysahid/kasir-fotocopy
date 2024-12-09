<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });

Route::get('/', fn() => Inertia::render('HomeView'))->name('home');
Route::get('/login', fn()  => Inertia::render('LoginView'))->name('login');
Route::get('/register', fn()  => Inertia::render('RegisterView'))->name('register');
Route::get('/create-store', fn()  => Inertia::render('customer/store/CreateStoreView'))->name('create-store');

Route::get('/dashboard', fn()  => Inertia::render('customer/main/DashboardView'))->name('dashboard');
Route::get('/product', fn()  => Inertia::render('customer/main/ProductView'))->name('product');
Route::get('/purchase', fn()  => Inertia::render('customer/main/PurchaseView'))->name('purchase');
Route::get('/sales', fn()  => Inertia::render('customer/main/SalesView'))->name('sales');
Route::get('/report', fn()  => Inertia::render('customer/main/ReportView'))->name('report');
Route::get('/setting', fn()  => Inertia::render('customer/main/SettingView'))->name('setting');
