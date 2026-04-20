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

Route::prefix('users')->middleware(['auth'])->group(function() {
    Route::get('/', 'UsersController@index')->name('users.index');
    Route::get('/{id}', 'UsersController@show')->name('users.show');
    Route::get('/{id}/edit', 'UsersController@edit')->name('users.edit');
    Route::put('/{id}', 'UsersController@update')->name('users.update');
    Route::delete('/{id}', 'UsersController@destroy')->name('users.destroy');
});
