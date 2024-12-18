<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\EnsureTokenIsValidLogout;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminOrderController;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware(EnsureTokenIsValidLogout::class);
    Route::post('/refresh', 'refresh')->middleware(EnsureTokenIsValid::class);
    Route::post('/register', 'register');
});

Route::middleware(EnsureTokenIsValid::class)->group(function () {
    Route::middleware(EnsureUserIsAdmin::class)->group(function () {
        Route::controller(AdminOrderController::class)->group(function () {
            Route::get('/admin/orders', 'index');
            Route::get('/admin/orders/{orderId}', 'get');
            Route::post('/admin/users/{userId}/orders', 'add');
            Route::put('/admin/orders/{orderId}', 'update');
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
        Route::get('/users/{id}', 'get');
        Route::patch('/users/{id}', 'update');
        Route::delete('/users/{id}', 'delete');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('/products/', 'index');
        Route::get('/products/{id}', 'get');
        Route::post('/products/', 'add');
        Route::patch('/products/{id}', 'update');
        Route::delete('/products/{id}','delete');
    });
});

