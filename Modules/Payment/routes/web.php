<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\MpesaCallbackController;
use Modules\Payment\Http\Controllers\PaymentController;

// Safaricom posts here unauthenticated — see bootstrap/app.php for the CSRF
// exemption this path needs. Daraja doesn't sign its callbacks, so the
// {secret} segment (checked in the controller) is what proves a POST here
// actually came from Safaricom rather than someone guessing/replaying a
// checkout_request_id — see MPESA_CALLBACK_SECRET in .env.
Route::post('/payments/mpesa/callback/{secret}', [MpesaCallbackController::class, 'handle'])->name('mpesa.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('payments', PaymentController::class)->names('payment');
});
