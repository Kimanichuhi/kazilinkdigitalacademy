<?php

namespace Modules\Payment\Providers;

use Modules\Payment\Console\ReconcileMpesaTransactions;
use Modules\Payment\Contracts\MpesaPaymentContract;
use Modules\Payment\Livewire\MpesaStatusPoller;
use Modules\Payment\Models\MpesaTransaction;
use Modules\Payment\Policies\MpesaTransactionPolicy;
use Modules\Payment\Services\MpesaPaymentService;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

class PaymentServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Payment';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'payment';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        ReconcileMpesaTransactions::class,
    ];

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

        $this->app->bind(MpesaPaymentContract::class, MpesaPaymentService::class);
    }

    public function boot(): void
    {
        parent::boot();

        Gate::policy(MpesaTransaction::class, MpesaTransactionPolicy::class);

        Livewire::component('payment::mpesa-status-poller', MpesaStatusPoller::class);
    }

    /**
     * Define module schedules.
     *
     * @param $schedule
     */
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command(ReconcileMpesaTransactions::class)->everyFiveMinutes()->withoutOverlapping();
    }
}
