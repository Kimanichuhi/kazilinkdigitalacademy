<?php

namespace Modules\Booking\Events;

use Illuminate\Foundation\Events\Dispatchable;

class BookingStatusChanged
{
    use Dispatchable;

    public function __construct(
        public string $bookingId,
        public string $bookingNumber,
        public string $fromStatus,
        public string $toStatus,
        public ?string $changedByUserId,
        public ?string $ipAddress,
        public ?string $userAgent,
    ) {}
}
