<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\SiteSettingLookupContract;
use Modules\Cms\Models\SiteSetting;

class SiteSettingLookupService implements SiteSettingLookupContract
{
    public function getMany(array $keys): array
    {
        return SiteSetting::whereIn('key', $keys)
            ->whereNotNull('value')
            ->pluck('value', 'key')
            ->all();
    }
}
