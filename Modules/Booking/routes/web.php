<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\Livewire\BookingWizard;
use Modules\Booking\Livewire\StudentDashboard;

// No auth required — matches the source's public /booking wizard exactly.
Route::get('/booking', BookingWizard::class)->name('booking.create');

// Source guard: any authenticated user may view /student, no role check
// (app/student/layout.tsx) — see MIGRATION-INVENTORY.md §4.
Route::middleware(['auth'])->get('/student', StudentDashboard::class)->name('student.dashboard');
