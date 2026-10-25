<?php

use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\PostController;
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
            Route::patch('/user/status/{id}', 'status');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/category', 'index');
            Route::post('/category', 'store');
            Route::get('/category/{id}', 'show');
            Route::put('/category/{id}', 'update');
            Route::delete('/category/{id}', 'delete');
            Route::patch('/category/status/{id}', 'status');
        });


        Route::controller(PostController::class)->group(function () {
            Route::get('/post', 'index');
            Route::post('/post', 'store');
            Route::get('/post/{id}', 'show');
            Route::put('/post', 'update');
            Route::delete('/post/{id}', 'delete');
        });
    });
});
