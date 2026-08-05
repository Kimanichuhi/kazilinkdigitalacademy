<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthController;

// The including RouteServiceProvider (Modules\Auth\Providers\RouteServiceProvider
// ::mapApiRoutes()) already wraps this whole file in ->prefix('api')->name('api.'),
// so names here resolve to e.g. "api.auth.login", not "api.api.auth.login".
Route::prefix('v1/auth')->name('auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('user', [AuthController::class, 'user'])->name('user');
    });
});
