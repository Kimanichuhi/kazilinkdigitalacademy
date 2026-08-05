<?php

namespace Modules\Core\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Modules\Cms\Contracts\SiteSettingLookupContract;
use Modules\Core\Support\OptionalContract;

class Footer extends Component
{
    public array $settings;

    public function __construct()
    {
        $settings = OptionalContract::resolve(SiteSettingLookupContract::class);

        $this->settings = $settings?->getMany([
            'site_name', 'footer_tagline', 'contact_email', 'contact_phone', 'contact_address',
            'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin', 'social_youtube',
        ]) ?? [];
    }

    public function render(): View
    {
        return view('core::components.footer');
    }
}
