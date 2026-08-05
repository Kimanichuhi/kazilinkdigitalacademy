<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\Api\ResourceController;

// The including RouteServiceProvider (Modules\Cms\Providers\RouteServiceProvider
// ::mapApiRoutes()) already wraps this whole file in ->prefix('api')->name('api.'),
// so names here resolve to e.g. "api.resources.index", not "api.api.resources.index".
Route::prefix('v1')->group(function () {
    Route::get('resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('resources/{id}', [ResourceController::class, 'show'])->name('resources.show');

    Route::middleware('auth:sanctum')
        ->post('resources/{id}/purchase', [ResourceController::class, 'purchase'])
        ->name('resources.purchase');
});
