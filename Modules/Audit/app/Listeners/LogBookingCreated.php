<?php

namespace Modules\Audit\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;
use Modules\Booking\Events\BookingCreated;

/**
 * Audit is a pure listener — it is never called synchronously by the
 * Booking module, only ever reacts to events. Disabling the Audit module
 * has zero effect on booking submission.
 */
class LogBookingCreated implements ShouldQueue
{
    public function handle(BookingCreated $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->userId,
                'action' => 'booking.created',
                'resource_type' => 'booking',
                'resource_id' => $event->bookingId,
                'new_values' => [
                    'booking_number' => $event->bookingNumber,
                    'full_name' => $event->fullName,
                    'email' => $event->email,
                    'payment_method' => $event->paymentMethod,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for booking '.$event->bookingNumber, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
