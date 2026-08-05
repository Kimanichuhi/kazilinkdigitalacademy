<?php

namespace Modules\Cms\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SiteSettingsUpdated
{
    use Dispatchable;

    /** @param list<string> $changedKeys */
    public function __construct(
        public ?string $userId,
        public array $changedKeys,
        public ?string $ipAddress,
        public ?string $userAgent,
    ) {}
}
