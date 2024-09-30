<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

    Route::controller(UserController::class)->group(function () {
        Route::get('/user/{id}', 'get');
        Route::patch('/user/{id}', 'update');
        Route::delete('/user/{id}', 'delete');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('/product/','index');
        Route::get('/product/{id}','get');
        Route::post('/product/',  'add');
        Route::patch('/product/{id}', 'update');
        Route::delete('/product/{id}','delete');
    });
});

