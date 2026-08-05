<?php

namespace Modules\Booking\Services;

use Modules\Booking\Enums\BookingStatus;
use Modules\Booking\Events\BookingStatusChanged;
use Modules\Booking\Models\Booking;

/**
 * The booking status state machine. Transitions are confirmed against the
 * source's actual behavior (MIGRATION-INVENTORY.md §7.7 and admin bookings
 * screen): the public wizard only ever produces draft/awaiting_payment;
 * paid/pending_approval/approved/rejected/completed are admin- or
 * payment-callback-triggered. `cancelled` is reachable from any
 * non-terminal state (a DB-allowed value in the source with no UI trigger
 * observed, but no reason to forbid it going forward).
 */
class BookingLifecycle
{
    /** @var array<string, list<string>> */
    private const TRANSITIONS = [
        'draft' => ['awaiting_payment', 'paid', 'cancelled'],
        'awaiting_payment' => ['paid', 'cancelled'],
        'paid' => ['pending_approval', 'cancelled'],
        'pending_approval' => ['approved', 'rejected', 'cancelled'],
        'approved' => ['completed', 'cancelled'],
        'rejected' => [],
        'cancelled' => [],
        'completed' => [],
    ];

    public function canTransition(Booking $booking, BookingStatus $to): bool
    {
        return in_array($to->value, self::TRANSITIONS[$booking->status->value] ?? [], true);
    }

    public function transition(Booking $booking, BookingStatus $to, ?string $reason = null): bool
    {
        if (! $this->canTransition($booking, $to)) {
            return false;
        }

        $fromStatus = $booking->status->value;
        $booking->status = $to;

        match ($to) {
            BookingStatus::Approved => $booking->approved_at = now(),
            BookingStatus::Rejected => $booking->rejection_reason = $reason,
            default => null,
        };

        $booking->save();

        BookingStatusChanged::dispatch(
            $booking->id,
            $booking->booking_number,
            $fromStatus,
            $to->value,
            auth()->id(),
            request()->ip(),
            request()->userAgent(),
        );

        return true;
    }
}
