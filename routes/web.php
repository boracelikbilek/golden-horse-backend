<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ScannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout',[AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('scanner',          [ScannerController::class, 'show'])->name('scanner');
        Route::post('scanner/resolve', [ScannerController::class, 'resolve'])->name('scanner.resolve');
        Route::post('scanner/order',   [ScannerController::class, 'createOrder'])->name('scanner.order');

        Route::get('orders',         [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::middleware('admin:tenant_owner,superadmin')->group(function () {
            Route::get('products',                [ProductController::class, 'index'])->name('products.index');
            Route::get('products/create',         [ProductController::class, 'create'])->name('products.create');
            Route::post('products',               [ProductController::class, 'store'])->name('products.store');
            Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('products/{product}',      [ProductController::class, 'update'])->name('products.update');
            Route::delete('products/{product}',   [ProductController::class, 'destroy'])->name('products.destroy');
        });
    });
});
