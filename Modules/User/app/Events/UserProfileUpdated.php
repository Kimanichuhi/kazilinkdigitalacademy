<?php

namespace Modules\User\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserProfileUpdated
{
    use Dispatchable;

    public function __construct(
        public string $userId,
        public array $oldValues,
        public array $newValues,
        public ?string $ipAddress,
        public ?string $userAgent,
    ) {}
}
