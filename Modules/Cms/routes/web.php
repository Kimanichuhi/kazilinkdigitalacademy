<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\AboutPageController;
use Modules\Cms\Http\Controllers\BlogShowController;
use Modules\Cms\Http\Controllers\CmsController;
use Modules\Cms\Http\Controllers\PageShowController;
use Modules\Cms\Http\Controllers\PricingPageController;
use Modules\Cms\Http\Controllers\ResourceDownloadController;
use Modules\Cms\Livewire\BlogIndexPage;
use Modules\Cms\Livewire\ContactPage;
use Modules\Cms\Livewire\FaqPage;
use Modules\Cms\Livewire\ResourcesPage;
use Modules\Cms\Livewire\SuccessStoriesPage;

// Public marketing pages — ported from _legacy-nextjs/app/(public)/*.
Route::get('/about', [AboutPageController::class, 'show'])->name('about');
Route::get('/contact', ContactPage::class)->name('contact');
Route::get('/faq', FaqPage::class)->name('faq');
Route::get('/pricing', [PricingPageController::class, 'show'])->name('pricing');
Route::get('/resources', ResourcesPage::class)->name('resources');
Route::get('/resources/{resource}/download', [ResourceDownloadController::class, 'download'])
    ->middleware('throttle:20,1')
    ->name('resources.download');
Route::get('/success-stories', SuccessStoriesPage::class)->name('success-stories');
Route::get('/blog', BlogIndexPage::class)->name('blog.index');
Route::get('/blog/{slug}', [BlogShowController::class, 'show'])->name('blog.show');

// Legal pages — Page+PageBlock backed, admin-editable via /admin/pages.
Route::get('/{slug}', [PageShowController::class, 'show'])
    ->whereIn('slug', ['privacy', 'terms', 'refund', 'ol-kalou-offer'])
    ->name('pages.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('cms', CmsController::class)->names('cms');
});
