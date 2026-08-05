<?php

namespace Modules\Notification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Events\BookingCreated;
use Modules\Notification\Models\Notification;

class CreateBookingNotification implements ShouldQueue
{
    public function handle(BookingCreated $event): void
    {
        if (! $event->userId) {
            return;
        }

        try {
            Notification::create([
                'user_id' => $event->userId,
                'title' => 'Booking Submitted',
                'message' => "Your booking {$event->bookingNumber} has been received. We'll be in touch within 24 hours.",
                'type' => 'success',
                'link' => '/student',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to create booking notification for '.$event->bookingNumber, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
