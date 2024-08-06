<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Product\AdminProductController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Middleware\BeforeRequest\EnsureAdminUser;
use App\Http\Middleware\BeforeRequest\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware(EnsureTokenIsValid::class);
});

Route::middleware(EnsureTokenIsValid::class)->group(function () {
    Route::controller(OrderController::class)->group(function () {

    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile/{id}', 'get');
        Route::post('/users/', 'add');
        Route::put('/users/{id}', 'update');
        Route::delete('/users/{id}', 'delete');
    });

    Route::middleware(EnsureAdminUser::class)->group(function () {
        Route::controller(AdminProductController::class)->group(function () {
            Route::get('/products/{id}','get');
            Route::post('/products/',  'add');
            Route::post('/products/{id}', 'update');
            Route::post('/products/{id}','delete');
        });
    });
});

