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
    Route::post('/auth/login', 'login')->name('auth.login');
    Route::post('/auth/logout', 'logout')->middleware(EnsureTokensAreValid::class)->name('auth.logout');
    Route::post('/auth/refresh', 'refresh')->middleware(EnsureRefreshTokenIsValid::class)->name('auth.refresh');
    Route::post('/auth/register', 'register')->name('auth.register');
});

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'controller' => AdminAuthController::class,
], function () {
    Route::post('/auth/login', 'login')->name('auth.login');
    Route::post('/auth/logout', 'logout')
        ->middleware([EnsureTokensAreValid::class, EnsureUserIsAdmin::class])
        ->name('auth.logout');
    Route::post('/auth/refresh', 'refresh')
        ->middleware([EnsureTokensAreValid::class, EnsureUserIsAdmin::class])
        ->name('auth.refresh');
});

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => [EnsureAccessTokenIsValid::class, EnsureUserIsAdmin::class],
], function () {
    Route::apiResource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
    Route::post('/users/{userId}/orders', [AdminOrderController::class, 'store'])->name('users.orders.store');

    Route::controller(AdminCartController::class)->group(function () {
        Route::get('/users/{userId}/cart', 'show')->name('users.cart.show');
        Route::put('/users/{userId}/cart', 'update')->name('users.cart.update');
        Route::delete('/users/{userId}/cart', 'destroy')->name('users.cart.destroy');
    });

    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('users', AdminUserController::class)->only(['show', 'update', 'destroy']);
});

Route::middleware(EnsureAccessTokenIsValid::class)->group(function () {
    Route::controller(OrderController::class)->group(function () {
        Route::get('/users/{userId}/orders', 'index')->name('users.orders.index');
        Route::get('/orders/{orderId}', 'show')->name('users.orders.show');
        Route::post('/users/{userId}/orders', 'store')->name('users.orders.store');
    });

    Route::controller(CartController::class)->group(function () {
        Route::get('/users/{userId}/cart', 'show')->name('users.cart.show');
        Route::put('/users/{userId}/cart', 'update')->name('users.cart.update');
        Route::delete('/users/{userId}/cart', 'destroy')->name('users.cart.destroy');
    });

    Route::apiResource('users', UserController::class)->only(['show', 'update', 'destroy']);
    Route::apiResource('products', ProductController::class)->only(['show', 'index']);
});

