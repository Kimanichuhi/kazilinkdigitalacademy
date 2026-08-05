<?php

namespace Modules\Audit\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;
use Modules\Booking\Events\BookingStatusChanged;

class LogBookingStatusChanged implements ShouldQueue
{
    public function handle(BookingStatusChanged $event): void
    {
        try {
            AuditLog::create([
                'user_id' => $event->changedByUserId,
                'action' => 'booking.status_changed',
                'resource_type' => 'booking',
                'resource_id' => $event->bookingId,
                'old_values' => ['status' => $event->fromStatus, 'booking_number' => $event->bookingNumber],
                'new_values' => ['status' => $event->toStatus, 'booking_number' => $event->bookingNumber],
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for booking status change '.$event->bookingNumber, ['exception' => $e->getMessage()]);
        }
    }
}
