<?php

namespace Modules\Cms\Providers;

use Modules\Cms\Contracts\BlogLookupContract;
use Modules\Cms\Contracts\FaqLookupContract;
use Modules\Cms\Contracts\NavigationLookupContract;
use Modules\Cms\Contracts\PageLookupContract;
use Modules\Cms\Contracts\PricingPlanLookupContract;
use Modules\Cms\Contracts\PurchaseLookupContract;
use Modules\Cms\Contracts\ResourceLookupContract;
use Modules\Cms\Contracts\SiteSettingLookupContract;
use Modules\Cms\Contracts\TestimonialLookupContract;
use Modules\Cms\Livewire\ResourcePurchaseDialog;
use Modules\Cms\Services\BlogLookupService;
use Modules\Cms\Services\FaqLookupService;
use Modules\Cms\Services\NavigationLookupService;
use Modules\Cms\Services\PageLookupService;
use Modules\Cms\Services\PricingPlanLookupService;
use Modules\Cms\Services\PurchaseLookupService;
use Modules\Cms\Services\ResourceLookupService;
use Modules\Cms\Services\SiteSettingLookupService;
use Modules\Cms\Services\TestimonialLookupService;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Models\BlogPost;
use Modules\Cms\Models\ContactSubmission;
use Modules\Cms\Models\Faq;
use Modules\Cms\Models\NavItem;
use Modules\Cms\Models\NavMenu;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\PageBlock;
use Modules\Cms\Models\Partner;
use Modules\Cms\Models\PricingPlan;
use Modules\Cms\Models\Resource;
use Modules\Cms\Models\SiteSetting;
use Modules\Cms\Models\TeamMember;
use Modules\Cms\Models\Testimonial;
use Modules\Cms\Policies\ContactSubmissionPolicy;
use Modules\Cms\Policies\PublicContentPolicy;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

class CmsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Cms';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'cms';

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

        $this->app->bind(NavigationLookupContract::class, NavigationLookupService::class);
        $this->app->bind(SiteSettingLookupContract::class, SiteSettingLookupService::class);
        $this->app->bind(TestimonialLookupContract::class, TestimonialLookupService::class);
        $this->app->bind(FaqLookupContract::class, FaqLookupService::class);
        $this->app->bind(ResourceLookupContract::class, ResourceLookupService::class);
        $this->app->bind(PurchaseLookupContract::class, PurchaseLookupService::class);
        $this->app->bind(BlogLookupContract::class, BlogLookupService::class);
        $this->app->bind(PricingPlanLookupContract::class, PricingPlanLookupService::class);
        $this->app->bind(PageLookupContract::class, PageLookupService::class);
    }

    public function boot(): void
    {
        parent::boot();

        foreach ([
            BlogCategory::class, BlogPost::class, Testimonial::class, Faq::class,
            Resource::class, Page::class, PageBlock::class, NavMenu::class, NavItem::class,
            SiteSetting::class, TeamMember::class, Partner::class, PricingPlan::class,
        ] as $model) {
            Gate::policy($model, PublicContentPolicy::class);
        }

        Gate::policy(ContactSubmission::class, ContactSubmissionPolicy::class);

        Livewire::component('cms::resource-purchase-dialog', ResourcePurchaseDialog::class);
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
