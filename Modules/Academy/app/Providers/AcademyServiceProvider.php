<?php

namespace Modules\Academy\Providers;

use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Contracts\ProgramCategoryLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Academy\Contracts\TrainerLookupContract;
use Modules\Academy\Models\Cohort;
use Modules\Academy\Models\Program;
use Modules\Academy\Models\ProgramCategory;
use Modules\Academy\Models\Trainer;
use Modules\Academy\Policies\CohortPolicy;
use Modules\Academy\Policies\ProgramCategoryPolicy;
use Modules\Academy\Policies\ProgramPolicy;
use Modules\Academy\Policies\TrainerPolicy;
use Modules\Academy\Services\CohortLookupService;
use Modules\Academy\Services\ProgramCategoryLookupService;
use Modules\Academy\Services\ProgramLookupService;
use Modules\Academy\Services\TrainerLookupService;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;

class AcademyServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Academy';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'academy';

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

        $this->app->bind(ProgramLookupContract::class, ProgramLookupService::class);
        $this->app->bind(CohortLookupContract::class, CohortLookupService::class);
        $this->app->bind(TrainerLookupContract::class, TrainerLookupService::class);
        $this->app->bind(ProgramCategoryLookupContract::class, ProgramCategoryLookupService::class);
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(ProgramCategory::class, ProgramCategoryPolicy::class);
        Gate::policy(Program::class, ProgramPolicy::class);
        Gate::policy(Trainer::class, TrainerPolicy::class);
        Gate::policy(Cohort::class, CohortPolicy::class);
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
