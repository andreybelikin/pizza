<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureAccessTokenIsValid;
use App\Http\Middleware\EnsureRefreshTokenIsValid;
use App\Http\Middleware\EnsureTokensAreValid;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminCartController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminAuthController;

Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/login', 'login');
    Route::post('/auth/logout', 'logout')->middleware(EnsureTokensAreValid::class);
    Route::post('/auth/refresh', 'refresh')->middleware(EnsureRefreshTokenIsValid::class);
    Route::post('/auth/register', 'register');
});

Route::controller(AdminAuthController::class)->group(function () {
    Route::post('/admin/auth/login', 'login');
    Route::post('/admin/auth/logout', 'logout')->middleware(EnsureTokensAreValid::class);
    Route::post('/admin/auth/refresh', 'refresh')->middleware(EnsureRefreshTokenIsValid::class);
})->middleware(EnsureUserIsAdmin::class);

Route::middleware(EnsureAccessTokenIsValid::class)->group(function () {
    Route::middleware(EnsureUserIsAdmin::class)->group(function () {
        Route::controller(AdminOrderController::class)->group(function () {
            Route::get('/admin/orders', 'index');
            Route::get('/admin/orders/{orderId}', 'get');
            Route::post('/admin/users/{userId}/orders', 'add');
            Route::put('/admin/orders/{orderId}', 'update');
        });

        Route::controller(AdminCartController::class)->group(function () {
            Route::get('/admin/users/{userId}/carts', 'get');
            Route::put('/admin/users/{userId}/carts', 'update');
            Route::post('/admin/products/', 'add');
            Route::delete('/admin/users/{userId}/carts', 'delete');
        });

        Route::controller(AdminProductController::class)->group(function () {
            Route::get('/admin/products/', 'index');
            Route::get('/admin/products/{id}', 'get');
            Route::post('/admin/products/', 'add');
            Route::put('/admin/products/{id}', 'update');
            Route::delete('/admin/products/{id}','delete');
        });

        Route::controller(AdminUserController::class)->group(function () {
            Route::get('/admin/users/{userId}', 'get');
            Route::put('/admin/users/{userId}', 'update');
            Route::delete('/admin/users/{userId}', 'delete');
        });
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/users/{userId}/orders', 'index');
        Route::get('/orders/{orderId}', 'get');
        Route::post('/users/{userId}/orders', 'add');
    });

    Route::controller(CartController::class)->group(function () {
        Route::get('/users/{userId}/carts', 'get');
        Route::put('/users/{userId}/carts', 'update');
        Route::delete('/users/{userId}/carts', 'delete');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users/{userId}', 'get');
        Route::put('/users/{userId}', 'update');
        Route::delete('/users/{userId}', 'delete');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('/products/', 'index');
        Route::get('/products/{id}', 'get');
    });
});

