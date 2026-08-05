<?php

namespace Modules\Booking\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\Booking\Models\Booking;

/**
 * Dispatched once a booking row is successfully persisted. Payment
 * initiation, notification, and audit logging all subscribe to this as
 * queued listeners — see ISOLATION RULES in the migration brief: none of
 * them may block or fail the primary booking submission.
 *
 * Carries a flat snapshot rather than the Booking model itself, so
 * listeners in other modules never touch Modules\Booking\Models\Booking
 * directly. Fields are extracted in the constructor (not a separate static
 * factory) so `BookingCreated::dispatch($booking)` — which Dispatchable
 * implements as `new static(...$args)` — populates the real instance that
 * gets queued, rather than constructing a second, empty one.
 */
class BookingCreated
{
    use Dispatchable;

    public string $bookingId;

    public string $bookingNumber;

    public ?string $userId;

    public string $fullName;

    public string $email;

    public string $phone;

    public ?string $totalAmount;

    public string $currency;

    public ?string $paymentMethod;

    public ?string $paymentReference;

    public function __construct(Booking $booking)
    {
        $this->bookingId = $booking->id;
        $this->bookingNumber = $booking->booking_number;
        $this->userId = $booking->user_id;
        $this->fullName = $booking->full_name;
        $this->email = $booking->email;
        $this->phone = $booking->phone;
        $this->totalAmount = $booking->total_amount;
        $this->currency = $booking->currency;
        $this->paymentMethod = $booking->payment_method;
        $this->paymentReference = $booking->payment_reference;
    }
}
