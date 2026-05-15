<?php

use Illuminate\Support\Facades\Route;
use Modules\GYM\app\Http\Controllers\Api\AuthController;
use Modules\GYM\app\Http\Controllers\Api\GymController;
use Modules\GYM\app\Http\Controllers\Api\GymRegistrationController;
use Modules\GYM\app\Http\Controllers\Api\GymPlanController;
use Modules\GYM\app\Http\Controllers\Api\MissingGymApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// USER-APP API PROTOCOLS (Discovery & Interactions)
Route::prefix('user-app')->group(function () {
    Route::get('gym/videos', [\Modules\GYM\app\Http\Controllers\Api\GymVideoController::class, 'index']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('interaction')->group(function () {
            Route::post('like', [\Modules\GYM\app\Http\Controllers\Api\InteractionController::class, 'toggleLike']);
            Route::post('comment', [\Modules\GYM\app\Http\Controllers\Api\InteractionController::class, 'storeComment']);
            Route::get('comments', [\Modules\GYM\app\Http\Controllers\Api\InteractionController::class, 'getComments']);
        });
    });
});

Route::prefix('provisions')->group(function () {
    Route::get('sections', [MissingGymApiController::class, 'provisionSections']);
});

Route::prefix('gym')->group(function () {
    // Missing Endpoints
    Route::get('dashboard/summary', [MissingGymApiController::class, 'dashboardSummary']);
    Route::post('attendance/check-in', [MissingGymApiController::class, 'checkIn']);
    Route::post('enquiries', [MissingGymApiController::class, 'storeEnquiry']);
    Route::post('reviews', [MissingGymApiController::class, 'storeReview']);
    Route::get('reports/revenue', [MissingGymApiController::class, 'revenueReport']);

    // Identity Protocols
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('login/verify', [AuthController::class, 'verifyLogin']);

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

    // Infrastructure Resources (Publicly discoverable for registration)
    Route::get('custom-fields', [GymController::class, 'getCustomFields']);

    // Infrastructure Management (Secured)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [GymController::class, 'index']);
        Route::post('store', [GymController::class, 'store']);

        // Commercial Plan Hub
        Route::prefix('plans')->group(function () {
            Route::get('/', [GymPlanController::class, 'index']);
            Route::post('/', [GymPlanController::class, 'store']);
            Route::get('{id}', [GymPlanController::class, 'show']);
            Route::put('{id}', [GymPlanController::class, 'update']);
        });

        // Member Management Hub
        Route::prefix('members')->group(function () {
            Route::get('/', [\Modules\GYM\app\Http\Controllers\Api\GymMemberController::class, 'index']);
            Route::post('/', [\Modules\GYM\app\Http\Controllers\Api\GymMemberController::class, 'store']);
            Route::get('{id}', [\Modules\GYM\app\Http\Controllers\Api\GymMemberController::class, 'show']);
        });

        Route::get('{id}', [GymController::class, 'show']);
        Route::put('{id}', [GymController::class, 'update']);
    });
});
