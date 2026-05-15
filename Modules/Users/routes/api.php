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
use Modules\Users\app\Http\Controllers\Api\UserProfileController;
use Modules\Users\app\Http\Controllers\Api\GymListingController;
use Modules\Users\app\Http\Controllers\Api\UserAppDiscoveryController;
use Modules\Users\app\Http\Controllers\Api\ExerciseLibraryController;
use Modules\Users\app\Http\Controllers\Api\MissingUserApiController;

Route::prefix('fcm')->group(function () {
    Route::post('/register-token', [MissingUserApiController::class, 'registerFcmToken']);
});

Route::prefix('user-app')->group(function () {
    // Discovery & Global Search Routes
    Route::get('/banners', [UserAppDiscoveryController::class, 'banners']);
    Route::get('/categories', [UserAppDiscoveryController::class, 'categories']);
    Route::get('/search', [UserAppDiscoveryController::class, 'search']);

    // Public Gym Listing Routes
    Route::get('/gyms/featured', [GymListingController::class, 'featured']);
    Route::get('/gyms', [GymListingController::class, 'index']);
    Route::get('/gyms/{identifier}', [GymListingController::class, 'show']);
    Route::get('/gyms/{identifier}/plans', [GymListingController::class, 'plans']);

    // Exercise Library
    Route::get('/exercises', [ExerciseLibraryController::class, 'index']);
    Route::get('/exercises/filters', [ExerciseLibraryController::class, 'filters']);
    Route::get('/exercises/{identifier}', [ExerciseLibraryController::class, 'show']);

    Route::post('/registration/step-1', [UserAppRegistrationController::class, 'step1']);
    Route::post('/registration/verify-otp', [UserAppRegistrationController::class, 'verifyOtp']);
    Route::post('/auth/login', [UserAppRegistrationController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [UserAppProfileController::class, 'me']);
        
        // Comprehensive Profile APIs
        Route::get('/profile', [UserProfileController::class, 'show']);
        Route::post('/profile', [UserProfileController::class, 'update']);
        
        Route::post('/registration/step-2', [UserAppRegistrationController::class, 'step2']);
        Route::post('/registration/step-3', [UserAppRegistrationController::class, 'step3']);
        Route::post('/registration/step-4', [UserAppRegistrationController::class, 'step4']);
        Route::post('/registration/step-5', [UserAppRegistrationController::class, 'step5']);

        Route::post('/profile/measurements', [UserAppProfileController::class, 'logMeasurement']);
        
        // Missing Endpoints
        Route::get('/profile/diet-plans', [MissingUserApiController::class, 'dietPlans']);
        Route::get('/profile/exercise-plans', [MissingUserApiController::class, 'exercisePlans']);
    });
});
