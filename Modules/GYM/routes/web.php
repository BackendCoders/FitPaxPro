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
    Route::get('/{uuid}/media', [GYMController::class, 'mediaSettings'])->name('gym.media');
    Route::get('/{uuid}/analytics', [GYMController::class, 'analytics'])->name('gym.analytics');
    Route::post('/{uuid}/media', [GYMController::class, 'updateMedia'])->name('gym.media.update');
    Route::delete('/media/{id}', [GYMController::class, 'destroyMedia'])->name('gym.media.destroy');
    Route::get('/{uuid}/edit', [GYMController::class, 'edit'])->name('gym.edit');
    Route::put('/{uuid}', [GYMController::class, 'update'])->name('gym.update');
    Route::delete('/{uuid}', [GYMController::class, 'destroy'])->name('gym.destroy');

    // Subscriptions
    Route::get('/subscriptions', [\Modules\GYM\app\Http\Controllers\SubscriptionController::class, 'index'])->name('gym.subscriptions.index');
    Route::get('/subscriptions/{id}', [\Modules\GYM\app\Http\Controllers\SubscriptionController::class, 'show'])->name('gym.subscriptions.show');

    // Membership Plans
    Route::resource('plans', \Modules\GYM\app\Http\Controllers\PlanController::class)->names('gym.plans');
    Route::post('/plans/{id}/toggle-status', [\Modules\GYM\app\Http\Controllers\PlanController::class, 'toggleStatus'])->name('gym.plans.toggle-status');
});
