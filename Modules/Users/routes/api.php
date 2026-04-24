<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/users', function (Request $request) {
    return $request->user();
});

use Modules\Users\app\Http\Controllers\Api\UserAppRegistrationController;
use Modules\Users\app\Http\Controllers\Api\UserAppProfileController;
use Modules\Users\app\Http\Controllers\Api\GymListingController;

Route::prefix('user-app')->group(function () {
    // Public Gym Listing Routes
    Route::get('/gyms', [GymListingController::class, 'index']);
    Route::get('/gyms/{identifier}', [GymListingController::class, 'show']);

    Route::post('/registration/step-1', [UserAppRegistrationController::class, 'step1']);
    Route::post('/registration/verify-otp', [UserAppRegistrationController::class, 'verifyOtp']);
    Route::post('/auth/login', [UserAppRegistrationController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/registration/step-2', [UserAppRegistrationController::class, 'step2']);
        Route::post('/registration/step-3', [UserAppRegistrationController::class, 'step3']);
        Route::post('/registration/step-4', [UserAppRegistrationController::class, 'step4']);
        Route::post('/registration/step-5', [UserAppRegistrationController::class, 'step5']);

        Route::post('/profile/measurements', [UserAppProfileController::class, 'logMeasurement']);
    });
});