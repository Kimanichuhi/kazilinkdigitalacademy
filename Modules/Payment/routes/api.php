<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\Api\PaymentController;

// The including RouteServiceProvider (Modules\Payment\Providers\RouteServiceProvider
// ::mapApiRoutes()) already wraps this whole file in ->prefix('api')->name('api.'),
// so names here resolve to e.g. "api.payments.status", not "api.api.payments.status".
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('payments/{checkoutRequestId}/status', [PaymentController::class, 'status'])->name('payments.status');
});
