<?php

namespace Modules\Booking\Enums;

/**
 * Lifecycle: draft -> awaiting_payment -> paid / pending_approval ->
 * approved / rejected -> completed (or cancelled at any point before
 * completed). Confirmed against the source admin bookings screen
 * (MIGRATION-INVENTORY.md §7.7): the only observed transitions there are
 * pending_approval -> approved|rejected and paid -> pending_approval.
 */
enum BookingStatus: string
{
    case Draft = 'draft';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::AwaitingPayment => 'Awaiting Payment',
            self::Paid => 'Payment Received',
            self::PendingApproval => 'Pending Approval',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
            self::Completed => 'Completed',
        };
    }
}
