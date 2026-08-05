<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PasswordChanged
{
    use Dispatchable;

    public function __construct(
        public string $userId,
        public ?string $ipAddress,
        public ?string $userAgent,
    ) {}
}
