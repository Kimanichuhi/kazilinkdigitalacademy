<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\MarketingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('marketings', MarketingController::class)->names('marketing');
});
