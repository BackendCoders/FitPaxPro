<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {
    Route::post('/login', 'AdminAuthController@apiLogin');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', 'AdminAuthController@apiMe');
        Route::post('/logout', 'AdminAuthController@apiLogout');

        Route::prefix('gym-operations')->group(function () {
            Route::get('/gyms', 'AdminGymOperationsApiController@gyms');
            Route::get('/attendance', 'AdminGymOperationsApiController@attendance');
            Route::get('/enquiries', 'AdminGymOperationsApiController@enquiries');
            Route::get('/fee-plans', 'AdminGymOperationsApiController@feePlans');
            Route::get('/gallery-media', 'AdminGymOperationsApiController@galleryMedia');
            Route::get('/reviews', 'AdminGymOperationsApiController@reviews');
        });

        Route::prefix('user-operations')->group(function () {
            Route::get('/users', 'AdminUserOperationsApiController@index');
            Route::post('/users', 'AdminUserOperationsApiController@store');
            Route::get('/users/{user}', 'AdminUserOperationsApiController@show');
            Route::put('/users/{user}', 'AdminUserOperationsApiController@update');
            Route::delete('/users/{user}', 'AdminUserOperationsApiController@destroy');
            Route::post('/users/{user}/activate', 'AdminUserOperationsApiController@activate');
            Route::post('/users/{user}/deactivate', 'AdminUserOperationsApiController@deactivate');
        });
    });
});
