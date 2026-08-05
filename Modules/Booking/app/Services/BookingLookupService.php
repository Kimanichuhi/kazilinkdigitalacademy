<?php

namespace Modules\Booking\Services;

use Modules\Booking\Contracts\BookingLookupContract;
use Modules\Booking\Enums\BookingStatus;
use Modules\Booking\Enums\PaymentStatus;
use Modules\Booking\Events\BookingPaymentStatusChanged;
use Modules\Booking\Models\Booking;

class BookingLookupService implements BookingLookupContract
{
    public function __construct(private readonly BookingLifecycle $lifecycle) {}

    public function find(string $id): ?array
    {
        $booking = Booking::find($id);

        if (! $booking) {
            return null;
        }

        return [
            'id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'email' => $booking->email,
            'full_name' => $booking->full_name,
            'total_amount' => $booking->total_amount,
            'currency' => $booking->currency,
            'status' => $booking->status->value,
            'payment_status' => $booking->payment_status->value,
        ];
    }

    public function markPaid(string $id, ?string $paymentReference, string $amountPaid): void
    {
        $booking = Booking::find($id);

        if (! $booking || $booking->payment_status === PaymentStatus::Paid) {
            return;
        }

        $booking->amount_paid = $amountPaid;
        $booking->payment_status = PaymentStatus::Paid;

        if ($paymentReference) {
            $booking->payment_reference = $paymentReference;
        }

        if (! $this->lifecycle->transition($booking, BookingStatus::Paid)) {
            $booking->save();
        }

        BookingPaymentStatusChanged::dispatch($booking->id, $booking->booking_number, 'paid', $paymentReference);
    }

    public function markPaymentFailed(string $id, ?string $reason = null): void
    {
        $booking = Booking::find($id);

        if (! $booking || $booking->payment_status === PaymentStatus::Paid) {
            return;
        }

        $booking->payment_status = PaymentStatus::Failed;
        $booking->save();

        BookingPaymentStatusChanged::dispatch($booking->id, $booking->booking_number, 'failed', null);
    }
}
