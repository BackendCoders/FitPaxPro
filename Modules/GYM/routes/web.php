<?php

use Illuminate\Support\Facades\Route;
use Modules\GYM\app\Http\Controllers\GYMController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix('gym')->middleware(['auth'])->group(function() {
    Route::get('/', [GYMController::class, 'index'])->name('gym.index');
    Route::get('/create', [GYMController::class, 'create'])->name('gym.create');
    Route::post('/store', [GYMController::class, 'store'])->name('gym.store');
    Route::get('/{uuid}/edit', [GYMController::class, 'edit'])->name('gym.edit');
    Route::put('/{uuid}', [GYMController::class, 'update'])->name('gym.update');
    Route::delete('/{uuid}', [GYMController::class, 'destroy'])->name('gym.destroy');
});
