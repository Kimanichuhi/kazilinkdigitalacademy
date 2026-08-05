<?php

use Illuminate\Support\Facades\Route;
use Modules\Academy\Http\Controllers\Api\ProgramController;

// The including RouteServiceProvider (Modules\Academy\Providers\RouteServiceProvider
// ::mapApiRoutes()) already wraps this whole file in ->prefix('api')->name('api.'),
// so names here resolve to e.g. "api.programs.index", not "api.api.programs.index".
Route::prefix('v1')->group(function () {
    Route::get('programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('programs/{slug}', [ProgramController::class, 'show'])->name('programs.show');
    Route::get('programs/{slug}/cohorts', [ProgramController::class, 'cohorts'])->name('programs.cohorts');
});
