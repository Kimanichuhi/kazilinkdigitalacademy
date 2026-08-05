<?php

namespace Modules\Booking\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dispatched when an admin reveals a booking's plaintext National ID.
 * ip/user-agent are captured synchronously at dispatch time (inside the
 * Livewire action that triggers this), not read later by the listener —
 * a queued listener has no HTTP request context, and this action is a
 * security control that must not silently lose that detail.
 */
class NationalIdRevealed
{
    use Dispatchable;

    public function __construct(
        public string $bookingId,
        public string $bookingNumber,
        public ?string $revealedByUserId,
        public ?string $ipAddress,
        public ?string $userAgent,
    ) {}
}
