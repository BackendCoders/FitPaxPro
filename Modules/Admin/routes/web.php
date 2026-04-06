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
        Route::get('/dashboard', 'AdminDashboardController@index')->name('dashboard');
        Route::get('/gym/create', 'AdminGymController@create')->name('gym.create');
        Route::post('/gym', 'AdminGymController@store')->name('gym.store');
        Route::get('/gym', 'AdminGymController@index')->name('gym.index');
        Route::get('/gym/{gym}/edit', 'AdminGymController@edit')->name('gym.edit-item');
        Route::put('/gym/{gym}', 'AdminGymController@update')->name('gym.update');
        Route::delete('/gym/{gym}', 'AdminGymController@destroy')->name('gym.destroy');
        Route::get('/gym/edit', 'AdminDashboardController@editGym')->name('gym.edit');
        Route::get('/gym/delete', 'AdminDashboardController@deleteGym')->name('gym.delete');
        Route::get('/gym/attendance', 'AdminDashboardController@attendance')->name('gym.attendance');
        Route::get('/gym/enquiry', 'AdminDashboardController@enquiry')->name('gym.enquiry');
        Route::get('/gym/fee-plans', 'AdminDashboardController@feePlans')->name('gym.fee-plans');
        Route::get('/gym/gallery-media', 'AdminDashboardController@galleryMedia')->name('gym.gallery-media');
        Route::get('/gym/reviews', 'AdminDashboardController@reviews')->name('gym.reviews');
        Route::post('/logout', 'AdminAuthController@logout')->name('logout');
    });

    Route::get('/', function () {
        return redirect()->route('admin.login');
    });
});
