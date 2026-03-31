<?php

use Illuminate\Support\Facades\Route;
use Modules\GYM\app\Http\Controllers\Api\AuthController;

Route::prefix('gym')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});
