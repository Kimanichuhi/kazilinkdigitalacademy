<?php

namespace Modules\Audit\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;
use Modules\Booking\Events\NationalIdRevealed;

/**
 * Deliberately NOT queued (unlike LogBookingCreated) — this logs a
 * security-sensitive action, so a failure to record it should be visible
 * in the same request rather than silently dropped in a background job.
 */
class LogNationalIdReveal
{
    public function handle(NationalIdRevealed $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->revealedByUserId,
                'action' => 'booking.id_revealed',
                'resource_type' => 'booking',
                'resource_id' => $event->bookingId,
                'new_values' => [
                    'booking_number' => $event->bookingNumber,
                ],
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for ID reveal on booking '.$event->bookingNumber, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
