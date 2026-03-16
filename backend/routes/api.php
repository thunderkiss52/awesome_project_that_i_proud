<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/subscription', [SubscriptionController::class, 'show']);

        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:booking-write');
        Route::put('/bookings/{id}', [BookingController::class, 'update'])->middleware('throttle:booking-write');
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->middleware('throttle:booking-write');
    });
});
