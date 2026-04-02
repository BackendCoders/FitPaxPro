<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', 'AdminAuthController@showLoginForm')->name('login');
    Route::post('/login', 'AdminAuthController@login')->name('login.submit');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', 'AdminAuthController@dashboard')->name('dashboard');
        Route::post('/logout', 'AdminAuthController@logout')->name('logout');
    });

    Route::get('/', function () {
        return redirect()->route('admin.login');
    });
});
