<?php

namespace Modules\Core\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Modules\Cms\Contracts\NavigationLookupContract;
use Modules\Cms\Contracts\SiteSettingLookupContract;
use Modules\Core\Support\OptionalContract;
use Modules\Marketing\Contracts\AdvertisementLookupContract;

class Navbar extends Component
{
    public array $navItems;

    public string $siteName;

    public ?string $announcement;

    public function __construct()
    {
        $navigation = OptionalContract::resolve(NavigationLookupContract::class);
        $settings = OptionalContract::resolve(SiteSettingLookupContract::class);
        $advertisements = OptionalContract::resolve(AdvertisementLookupContract::class);

        $this->navItems = $navigation?->itemsForLocation('header') ?? [];

        $siteSettings = $settings?->getMany(['site_name']) ?? [];
        $this->siteName = $siteSettings['site_name'] ?? 'Kazilink Digital Academy';

        $ads = $advertisements?->listActiveByType('announcement_bar', 1) ?? [];
        $this->announcement = $ads[0]['title'] ?? null;
    }

    public function render(): View
    {
        return view('core::components.navbar');
    }
}
