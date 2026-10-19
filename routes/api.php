<?php

use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::prefix('v1')
    ->controller(AuthController::class)
    ->group(function () {

        Route::post('/register', 'register')
            ->middleware('throttle:5,1');

        Route::post('/login', 'login')
            ->middleware('throttle:login');

        Route::post('/forgot-password', 'resetPasswordLink')
            ->middleware('throttle:5,1');

        Route::post('/reset-password', 'completePasswordReset')
            ->middleware('throttle:5,1');

        Route::middleware('auth:sanctum')->group(function () {

            // Accessible before email verification
            Route::post('/verify', 'emailVerify')
                ->middleware('throttle:5,1');

            // Resend OTP
            Route::post('/resend-otp', 'resendOtp')->middleware('throttle:5,1');

            Route::post('/logout', 'logout');

            // Requires a verified email
            Route::get('/me', 'authenticatedUser')
                ->middleware('verified');
        });
    });

// Admin Routes
Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {


        Route::controller(UserController::class)->group(function () {
            Route::get('/user', 'index');
            Route::post('/user', 'store');
            Route::get('/user/{id}', 'show');
            Route::put('/user/{id}', 'update');
            Route::delete('/user/{id}', 'delete');
        });
    });
});
