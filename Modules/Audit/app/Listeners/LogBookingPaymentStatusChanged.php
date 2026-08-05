<?php

namespace Modules\Audit\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Audit\Models\AuditLog;
use Modules\Booking\Events\BookingPaymentStatusChanged;

/**
 * Fires from the (unauthenticated) M-Pesa callback path, so there is no
 * acting user to attribute this to — logged with a null user_id.
 */
class LogBookingPaymentStatusChanged implements ShouldQueue
{
    public function handle(BookingPaymentStatusChanged $event): void
    {
        try {
            AuditLog::create([
                'action' => 'booking.payment_status_changed',
                'resource_type' => 'booking',
                'resource_id' => $event->bookingId,
                'new_values' => [
                    'booking_number' => $event->bookingNumber,
                    'payment_status' => $event->paymentStatus,
                    'payment_reference' => $event->paymentReference,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write audit log for payment status change '.$event->bookingNumber, ['exception' => $e->getMessage()]);
        }
    }
}
