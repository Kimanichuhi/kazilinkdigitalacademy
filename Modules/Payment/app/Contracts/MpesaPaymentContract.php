<?php

namespace Modules\Payment\Contracts;

/**
 * Lets other modules (Booking, and later Cms for resource purchases)
 * trigger and check on an M-Pesa STK push without depending on
 * Modules\Payment's models/services directly — mirrors the
 * BookingLookupContract pattern Payment itself depends on for the reverse
 * direction, keeping the module dependency graph one-way in both cases.
 */
interface MpesaPaymentContract
{
    /**
     * Creates a pending transaction row (tied to a booking) and attempts an
     * STK push immediately. The transaction row is created even if the push
     * itself fails or Daraja isn't configured, so it's still visible for
     * manual reconciliation.
     *
     * @return array{checkout_request_id: ?string, error: ?string}
     */
    public function initiateForBooking(string $bookingId, string $phone, string $amount, string $accountReference, string $description): array;

    /**
     * Same as initiateForBooking(), but ties the transaction to a resource
     * purchase instead.
     *
     * @return array{checkout_request_id: ?string, error: ?string}
     */
    public function initiateForPurchase(string $purchaseId, string $phone, string $amount, string $accountReference, string $description): array;

    /**
     * @return array{status: string, result_desc: ?string}|null Null if no transaction exists yet for this checkout_request_id.
     */
    public function status(string $checkoutRequestId): ?array;
}
