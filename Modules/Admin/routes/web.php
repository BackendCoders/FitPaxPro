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
        Route::get('/gym/attendance', 'AdminGymController@attendance')->name('gym.attendance');
        Route::get('/gym/attendance/{attendance}', 'AdminGymController@attendanceView')->name('gym.attendance.view');
        Route::get('/gym/edit', 'AdminDashboardController@editGym')->name('gym.edit');
        Route::get('/gym/delete', 'AdminDashboardController@deleteGym')->name('gym.delete');
        Route::get('/gym/enquiry', 'AdminGymController@enquiries')->name('gym.enquiry');
        Route::get('/gym/enquiry/{enquiry}', 'AdminGymController@enquiryView')->name('gym.enquiry.view');
        Route::get('/gym/enquiry/{enquiry}/edit', 'AdminGymController@editEnquiry')->name('gym.enquiry.edit');
        Route::put('/gym/enquiry/{enquiry}', 'AdminGymController@updateEnquiry')->name('gym.enquiry.update');
        Route::delete('/gym/enquiry/{enquiry}', 'AdminGymController@destroyEnquiry')->name('gym.enquiry.destroy');
        Route::get('/gym/fee-plans', 'AdminGymController@feePlans')->name('gym.fee-plans');
        Route::get('/gym/fee-plans/{feePlan}', 'AdminGymController@feePlanView')->name('gym.fee-plans.view');
        Route::post('/gym/fee-plans/{feePlan}/approve', 'AdminGymController@approveFeePlan')->name('gym.fee-plans.approve');
        Route::post('/gym/fee-plans/{feePlan}/disapprove', 'AdminGymController@disapproveFeePlan')->name('gym.fee-plans.disapprove');
        Route::get('/gym/trainers', 'AdminGymController@trainers')->name('gym.trainers');
        Route::get('/gym/trainers/{trainer}/edit', 'AdminGymController@editTrainer')->name('gym.trainers.edit');
        Route::put('/gym/trainers/{trainer}', 'AdminGymController@updateTrainer')->name('gym.trainers.update');
        Route::delete('/gym/trainers/{trainer}', 'AdminGymController@destroyTrainer')->name('gym.trainers.destroy');
        Route::get('/gym/gallery-media', 'AdminGymController@galleryMedia')->name('gym.gallery-media');
        Route::get('/gym/gallery-media/{galleryMedia}', 'AdminGymController@galleryMediaView')->name('gym.gallery-media.view');
        Route::get('/gym/reviews', 'AdminGymController@reviews')->name('gym.reviews');
        Route::get('/gym/reviews/{review}', 'AdminGymController@reviewView')->name('gym.reviews.view');
        Route::post('/logout', 'AdminAuthController@logout')->name('logout');
    });

    Route::get('/', function () {
        return redirect()->route('admin.login');
    });
});
