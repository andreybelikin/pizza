<?php

use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Middleware\EnsureAdminUser;
use Illuminate\Support\Facades\Route;

Route::get('/products/', [ProfileController::class], 'index');
Route::get('/products/{id}', [ProfileController::class], 'get');

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout');
});

Route::middleware('EnsureTokenIsValid')->group(function () {
    Route::controller(OrderController::class)->group(function () {

    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/users/{id}', 'get');

        Route::post('/users/','store');

        Route::put('/users/{id}', 'update');

        Route::delete('/users/{id}', 'delete');
    });

    Route::middleware([EnsureAdminUser::class])->group(function () {
        Route::get('/products/{id}', [AdminProductController::class, 'get']);
    });
});

