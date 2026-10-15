<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->middleware('throttle:5,1');
        Route::post('/login', 'login')->middleware('throttle:5,1');

        // For Authenticated User
        Route::middleware(['auth:sanctum', 'verified'])->group(function () {
            Route::get('/me', 'authenticatedUser');
            Route::post('/logout', 'logout');

            Route::post('/verify', 'emailVerify')->middleware('throttle:5,1');
        });
    });
});
