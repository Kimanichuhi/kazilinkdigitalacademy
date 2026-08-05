<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;
use Modules\User\Livewire\ActiveSessions;
use Modules\User\Livewire\StudentProfile;

// Ports _legacy-nextjs/app/student/profile/page.tsx — previously missing
// entirely (the /student dashboard's "Edit Profile" link 404'd).
Route::middleware('auth')->get('/student/profile', StudentProfile::class)->name('student.profile');

Route::middleware('auth')->get('/student/profile/sessions', ActiveSessions::class)->name('student.sessions');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class)->names('user');
});
