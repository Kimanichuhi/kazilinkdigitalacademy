<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Notification\Livewire\NotificationsIndex;

// Matches the Navbar's user-dropdown "Notifications" link exactly
// (previously a dead /student/notifications 404 in the source — see
// MIGRATION-INVENTORY.md §7 discrepancy #4).
Route::middleware('auth')->get('/student/notifications', NotificationsIndex::class)->name('notifications.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('notifications', NotificationController::class)->names('notification');
});
