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

    // Dynamic Custom Fields
    Route::post('custom-fields/{custom_field}/toggle-status', [Modules\Admin\app\Http\Controllers\CustomFieldController::class, 'toggleStatus'])->name('admin.custom-fields.toggle-status');
    Route::resource('custom-fields', Modules\Admin\app\Http\Controllers\CustomFieldController::class)->names('admin.custom-fields');

    // Banner Management
    Route::post('banners/{banner}/toggle-status', [Modules\Admin\app\Http\Controllers\BannerController::class, 'toggleStatus'])->name('admin.banners.toggle-status');
    Route::resource('banners', Modules\Admin\app\Http\Controllers\BannerController::class)->names('admin.banners');

    // Category Management
    Route::post('categories/{category}/toggle-status', [Modules\Admin\app\Http\Controllers\CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
    Route::resource('categories', Modules\Admin\app\Http\Controllers\CategoryController::class)->names('admin.categories');
});
