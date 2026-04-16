<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('admin')->middleware(['auth'])->group(function() {
    Route::get('/', 'AdminController@index');
    Route::get('/dashboard', 'DashboardController@index')->name('admin.dashboard');
    
    // Global Settings
    Route::get('/settings', [Modules\Admin\app\Http\Controllers\SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [Modules\Admin\app\Http\Controllers\SettingController::class, 'update'])->name('admin.settings.update');
});
