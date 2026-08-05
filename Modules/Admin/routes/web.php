<?php

use Illuminate\Support\Facades\Route;
use Modules\Academy\Livewire\Admin\CohortsIndex;
use Modules\Academy\Livewire\Admin\ProgramsIndex;
use Modules\Academy\Livewire\Admin\TrainersIndex;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Audit\Livewire\Admin\AuditLogsIndex;
use Modules\Booking\Livewire\Admin\BookingsIndex;
use Modules\Cms\Livewire\Admin\BlogPostsIndex;
use Modules\Cms\Livewire\Admin\ContactSubmissionsIndex;
use Modules\Cms\Livewire\Admin\FaqsIndex;
use Modules\Cms\Livewire\Admin\PagesIndex;
use Modules\Cms\Livewire\Admin\PricingPlansIndex;
use Modules\Cms\Livewire\Admin\PurchasesIndex;
use Modules\Cms\Livewire\Admin\ResourcesIndex;
use Modules\Cms\Livewire\Admin\SiteSettingsForm;
use Modules\Cms\Livewire\Admin\TeamMembersIndex;
use Modules\Cms\Livewire\Admin\TestimonialsIndex;
use Modules\Marketing\Livewire\Admin\AdvertisementsIndex;
use Modules\Marketing\Livewire\Admin\CtasIndex;
use Modules\Marketing\Livewire\Admin\StatisticsIndex;
use Modules\Notification\Livewire\Admin\NotificationsIndex;
use Modules\User\Livewire\Admin\StudentsIndex;
use Modules\User\Livewire\Admin\UsersIndex;

Route::middleware(['auth', 'admin.family'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');

    // Core operational screens
    Route::get('/bookings', BookingsIndex::class)->name('admin.bookings');
    Route::get('/students', StudentsIndex::class)->name('admin.students');
    Route::get('/cohorts', CohortsIndex::class)->name('admin.cohorts');
    Route::get('/programs', ProgramsIndex::class)->name('admin.programs');
    Route::get('/trainers', TrainersIndex::class)->name('admin.trainers');
    Route::get('/users', UsersIndex::class)->name('admin.users');

    // Marketing & CMS
    Route::get('/stats', StatisticsIndex::class)->name('admin.stats');
    Route::get('/advertisements', AdvertisementsIndex::class)->name('admin.advertisements');
    Route::get('/ctas', CtasIndex::class)->name('admin.ctas');
    Route::get('/blog', BlogPostsIndex::class)->name('admin.blog');
    Route::get('/testimonials', TestimonialsIndex::class)->name('admin.testimonials');
    Route::get('/faqs', FaqsIndex::class)->name('admin.faqs');
    Route::get('/resources', ResourcesIndex::class)->name('admin.resources');
    Route::get('/purchases', PurchasesIndex::class)->name('admin.purchases');
    Route::get('/pricing-plans', PricingPlansIndex::class)->name('admin.pricing-plans');
    Route::get('/team-members', TeamMembersIndex::class)->name('admin.team-members');
    Route::get('/pages', PagesIndex::class)->name('admin.pages');
    Route::get('/contact-submissions', ContactSubmissionsIndex::class)->name('admin.contact-submissions');

    // Navigation — source screen is itself an unbuilt "coming soon" stub
    // (MIGRATION-INVENTORY.md / Phase 7 extraction), ported identically.
    Route::get('/menus', fn () => view('admin::menus'))->name('admin.menus');

    Route::get('/settings', SiteSettingsForm::class)->name('admin.settings');

    // System
    Route::get('/notifications', NotificationsIndex::class)->name('admin.notifications');
    Route::get('/audit-log', AuditLogsIndex::class)->name('admin.audit-log');
});
