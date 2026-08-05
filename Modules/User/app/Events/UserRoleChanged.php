<?php

namespace Modules\User\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserRoleChanged
{
    use Dispatchable;

    public function __construct(
        public string $targetUserId,
        public ?string $oldRole,
        public string $newRole,
        public ?string $changedByUserId,
        public ?string $ipAddress,
        public ?string $userAgent,
    ) {}
}
