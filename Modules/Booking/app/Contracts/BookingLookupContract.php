<?php

namespace Modules\Booking\Contracts;

interface BookingLookupContract
{
    /**
     * @return array{id: string, booking_number: string, email: string, full_name: string, total_amount: ?string, currency: string, status: string, payment_status: string}|null
     */
    public function find(string $id): ?array;

    /**
     * Marks a booking as paid and transitions its status via the lifecycle
     * state machine. Idempotent — safe to call more than once for the same
     * booking (e.g. a Daraja callback replay, or a callback followed by a
     * reconciliation-command query hitting the same transaction).
     */
    public function markPaid(string $id, ?string $paymentReference, string $amountPaid): void;

    /**
     * Records a failed/cancelled payment attempt without touching the
     * booking's status — the booking stays in draft/awaiting_payment so the
     * customer (or an admin) can retry payment.
     */
    public function markPaymentFailed(string $id, ?string $reason = null): void;
}
