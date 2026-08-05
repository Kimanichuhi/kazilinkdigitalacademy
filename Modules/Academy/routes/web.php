<?php

use Illuminate\Support\Facades\Route;
use Modules\Academy\Http\Controllers\ProgramShowController;
use Modules\Academy\Http\Controllers\TrainerDashboardController;
use Modules\Academy\Livewire\CohortsIndex;
use Modules\Academy\Livewire\ProgramsIndex;

Route::get('/programs', ProgramsIndex::class)->name('programs.index');
Route::get('/programs/{slug}', [ProgramShowController::class, 'show'])->name('programs.show');
Route::get('/cohorts', CohortsIndex::class)->name('cohorts.index');

Route::middleware(['auth', 'trainer.access'])->get('/trainer', [TrainerDashboardController::class, 'index'])->name('trainer.dashboard');
