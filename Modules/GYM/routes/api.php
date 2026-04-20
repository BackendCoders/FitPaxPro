<?php

use Illuminate\Support\Facades\Route;
use Modules\GYM\app\Http\Controllers\Api\AuthController;
use Modules\GYM\app\Http\Controllers\Api\GymController;
use Modules\GYM\app\Http\Controllers\Api\GymRegistrationController;
use Modules\GYM\app\Http\Controllers\Api\GymPlanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('gym')->group(function () {
    // Identity Protocols
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // THE 5-STEP INFRASTRUCTURE PROVISIONING PROTOCOL
    Route::prefix('registration')->group(function () {
        // Step 1: Identity & OTP Signal (Public)
        Route::post('step-1', [GymRegistrationController::class, 'step1']);
        Route::post('verify-otp', [GymRegistrationController::class, 'verifyOtp']);
        
        // Secured Calibration (Steps 2-5)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('step-2', [GymRegistrationController::class, 'step2']); // Plans
            Route::post('step-3', [GymRegistrationController::class, 'step3']); // Visuals
            Route::post('step-4', [GymRegistrationController::class, 'step4']); // Geographic
            Route::post('step-5', [GymRegistrationController::class, 'step5']); // Intelligence
        });
    });

    // Infrastructure Management (Secured)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [GymController::class, 'index']);
        Route::post('store', [GymController::class, 'store']);
        Route::get('{id}', [GymController::class, 'show']);
        Route::put('{id}', [GymController::class, 'update']);

        // Commercial Plan Hub
        Route::prefix('plans')->group(function () {
            Route::post('/', [GymPlanController::class, 'store']);
            Route::put('{id}', [GymPlanController::class, 'update']);
        });
    });
});
