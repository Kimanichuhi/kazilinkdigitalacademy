<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\Api\BookingController;

// The including RouteServiceProvider (Modules\Booking\Providers\RouteServiceProvider
// ::mapApiRoutes()) already wraps this whole file in ->prefix('api')->name('api.'),
// so names here resolve to e.g. "api.bookings.index", not "api.api.bookings.index".
//
// Auth-required throughout — no guest/anonymous bookings via the API (see
// BookingController's class docblock).
Route::middleware('auth:sanctum')->prefix('v1')->name('bookings.')->group(function () {
    Route::get('bookings', [BookingController::class, 'index'])->name('index');
    Route::post('bookings', [BookingController::class, 'store'])->name('store');
    Route::get('bookings/{id}', [BookingController::class, 'show'])->name('show');
    Route::post('bookings/{id}/pay', [BookingController::class, 'pay'])->name('pay');
    Route::post('bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('confirm');
});
