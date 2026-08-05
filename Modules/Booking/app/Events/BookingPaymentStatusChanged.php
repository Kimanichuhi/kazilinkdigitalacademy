<?php

namespace Modules\Booking\Events;

use Illuminate\Foundation\Events\Dispatchable;

class BookingPaymentStatusChanged
{
    use Dispatchable;

    public function __construct(
        public string $bookingId,
        public string $bookingNumber,
        public string $paymentStatus,
        public ?string $paymentReference,
    ) {}
}
