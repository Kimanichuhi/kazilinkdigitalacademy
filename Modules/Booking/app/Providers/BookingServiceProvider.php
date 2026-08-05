<?php

namespace Modules\Booking\Providers;

use Modules\Booking\Contracts\BookingLookupContract;
use Modules\Booking\Models\Booking;
use Modules\Booking\Policies\BookingPolicy;
use Modules\Booking\Services\BookingLookupService;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;

class BookingServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Booking';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'booking';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(BookingLookupContract::class, BookingLookupService::class);
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(Booking::class, BookingPolicy::class);
    }

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
