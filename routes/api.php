<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Product\AdminProductController;
use App\Http\Controllers\Profile\AdminProfileController;
use App\Http\Controllers\Profile\OrderController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Middleware\BeforeRequest\EnsureAdminUser;
use App\Http\Middleware\BeforeRequest\EnsureTokenIsValid;
use App\Http\Middleware\BeforeRequest\EnsureTokenIsValidLogout;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware(EnsureTokenIsValidLogout::class);
    Route::post('/refresh', 'refresh')->middleware(EnsureTokenIsValid::class);
    Route::post('/register', 'register');
});

Route::middleware(EnsureTokenIsValid::class)->group(function () {
    Route::controller(OrderController::class)->group(function () {

    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile/{id}', 'get');
        Route::post('/profile/', 'add');
        Route::put('/profile/{id}', 'update');
        Route::delete('/profile/{id}', 'delete');
    });

    Route::middleware(EnsureAdminUser::class)->group(function () {
        Route::controller(AdminProductController::class)->group(function () {
            Route::get('/admin/products/{id}','get');
            Route::post('/admin/products/',  'add');
            Route::post('/admin/products/{id}', 'update');
            Route::post('/admin/products/{id}','delete');
        });

        Route::post('/admin/profile/{id}', [AdminProfileController::class, 'update']);
    });
});

