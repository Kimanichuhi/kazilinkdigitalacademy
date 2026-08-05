<?php

namespace Modules\Marketing\Providers;

use Modules\Marketing\Contracts\AdvertisementLookupContract;
use Modules\Marketing\Contracts\CtaLookupContract;
use Modules\Marketing\Contracts\StatisticLookupContract;
use Modules\Marketing\Models\Advertisement;
use Modules\Marketing\Models\Cta;
use Modules\Marketing\Models\Statistic;
use Modules\Marketing\Policies\PublicContentPolicy;
use Modules\Marketing\Services\AdvertisementLookupService;
use Modules\Marketing\Services\CtaLookupService;
use Modules\Marketing\Services\StatisticLookupService;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;

class MarketingServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Marketing';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'marketing';

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

        $this->app->bind(StatisticLookupContract::class, StatisticLookupService::class);
        $this->app->bind(AdvertisementLookupContract::class, AdvertisementLookupService::class);
        $this->app->bind(CtaLookupContract::class, CtaLookupService::class);
    }

    public function boot(): void
    {
        parent::boot();

        foreach ([Advertisement::class, Cta::class, Statistic::class] as $model) {
            Gate::policy($model, PublicContentPolicy::class);
        }
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
