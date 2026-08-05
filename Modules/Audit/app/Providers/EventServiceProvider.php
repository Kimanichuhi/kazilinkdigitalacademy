<?php

namespace Modules\Audit\Providers;

use App\Events\PasswordChanged;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Audit\Listeners\LogBookingCreated;
use Modules\Audit\Listeners\LogBookingPaymentStatusChanged;
use Modules\Audit\Listeners\LogBookingStatusChanged;
use Modules\Audit\Listeners\LogNationalIdReveal;
use Modules\Audit\Listeners\LogPasswordChanged;
use Modules\Audit\Listeners\LogPasswordReset;
use Modules\Audit\Listeners\LogSiteSettingsUpdated;
use Modules\Audit\Listeners\LogUserLogin;
use Modules\Audit\Listeners\LogUserLogout;
use Modules\Audit\Listeners\LogUserProfileUpdated;
use Modules\Audit\Listeners\LogUserRegistered;
use Modules\Audit\Listeners\LogUserRoleChanged;
use Modules\Booking\Events\BookingCreated;
use Modules\Booking\Events\BookingPaymentStatusChanged;
use Modules\Booking\Events\BookingStatusChanged;
use Modules\Booking\Events\NationalIdRevealed;
use Modules\Cms\Events\SiteSettingsUpdated;
use Modules\User\Events\UserProfileUpdated;
use Modules\User\Events\UserRoleChanged;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        BookingCreated::class => [
            LogBookingCreated::class,
        ],
        NationalIdRevealed::class => [
            LogNationalIdReveal::class,
        ],
        Login::class => [
            LogUserLogin::class,
        ],
        Logout::class => [
            LogUserLogout::class,
        ],
        PasswordReset::class => [
            LogPasswordReset::class,
        ],
        Registered::class => [
            LogUserRegistered::class,
        ],
        PasswordChanged::class => [
            LogPasswordChanged::class,
        ],
        UserProfileUpdated::class => [
            LogUserProfileUpdated::class,
        ],
        UserRoleChanged::class => [
            LogUserRoleChanged::class,
        ],
        SiteSettingsUpdated::class => [
            LogSiteSettingsUpdated::class,
        ],
        BookingStatusChanged::class => [
            LogBookingStatusChanged::class,
        ],
        BookingPaymentStatusChanged::class => [
            LogBookingPaymentStatusChanged::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
