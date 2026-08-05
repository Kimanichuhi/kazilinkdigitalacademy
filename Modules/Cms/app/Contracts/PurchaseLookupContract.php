<?php

namespace Modules\Cms\Contracts;

/**
 * Mirrors Modules\Booking\Contracts\BookingLookupContract's markPaid/
 * markPaymentFailed shape — this is what Modules\Payment's callback
 * controller and reconciliation command call for a purchase-backed
 * transaction, the same way they already do for bookings.
 */
interface PurchaseLookupContract
{
    /**
     * Idempotent — safe to call more than once for the same purchase (a
     * Daraja callback replay, or a callback followed by a reconciliation
     * query hitting the same transaction).
     */
    public function markPaid(string $id, ?string $paymentId, string $amountPaid): void;

    public function markPaymentFailed(string $id, ?string $reason = null): void;
}
