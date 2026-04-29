<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\QrController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth (public)
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);

    // Public catalog (guest mode for browsing)
    Route::get('/categories',       [CatalogController::class, 'categories']);
    Route::get('/products',         [CatalogController::class, 'products']);
    Route::get('/products/{slug}',  [CatalogController::class, 'product']);
    Route::get('/stores',           [CatalogController::class, 'stores']);
    Route::get('/campaigns',        [CatalogController::class, 'campaigns']);
    Route::get('/reward-levels',    [CatalogController::class, 'rewardLevels']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me',              [AuthController::class, 'me']);
        Route::get('/me/stats',        [MeController::class, 'stats']);
        Route::get('/me/orders',       [MeController::class, 'orders']);
        Route::get('/me/transactions', [MeController::class, 'transactions']);
        Route::get('/badges',       [CatalogController::class, 'badges']);
        Route::post('/qr/rotate',   [QrController::class, 'rotate']);
    });
});
