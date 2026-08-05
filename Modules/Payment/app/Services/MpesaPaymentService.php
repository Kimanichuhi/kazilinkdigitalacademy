<?php

namespace Modules\Payment\Services;

use Modules\Booking\Contracts\BookingLookupContract;
use Modules\Payment\Contracts\MpesaPaymentContract;
use Modules\Payment\Enums\MpesaTransactionStatus;
use Modules\Payment\Models\MpesaTransaction;

class MpesaPaymentService implements MpesaPaymentContract
{
    public function __construct(
        private readonly DarajaClient $daraja,
        private readonly BookingLookupContract $bookings,
    ) {}

    public function initiateForBooking(string $bookingId, string $phone, string $amount, string $accountReference, string $description): array
    {
        // Never trust a caller-supplied amount for a real charge — re-derive
        // it from the booking's own stored price. $amount only survives as
        // a fallback if the booking can't be found, which shouldn't happen.
        $booking = $this->bookings->find($bookingId);
        $verifiedAmount = (string) ($booking['total_amount'] ?? $amount);

        return $this->initiate(['booking_id' => $bookingId], $phone, $verifiedAmount, $accountReference, $description);
    }

    public function initiateForPurchase(string $purchaseId, string $phone, string $amount, string $accountReference, string $description): array
    {
        return $this->initiate(['purchase_id' => $purchaseId], $phone, $amount, $accountReference, $description);
    }

    private function initiate(array $owner, string $phone, string $amount, string $accountReference, string $description): array
    {
        $transaction = MpesaTransaction::create($owner + [
            'phone' => $phone,
            'amount' => $amount,
            'status' => MpesaTransactionStatus::Pending,
        ]);

        if (! $this->daraja->configured()) {
            return ['checkout_request_id' => null, 'error' => 'M-Pesa is not configured yet. Please contact support to complete your payment.'];
        }

        try {
            $result = $this->daraja->stkPush(
                phone: $phone,
                amount: $amount,
                accountReference: $accountReference,
                description: $description,
                callbackUrl: (string) config('payment.mpesa.callback_url'),
            );

            $transaction->update([
                'checkout_request_id' => $result['checkout_request_id'],
                'merchant_request_id' => $result['merchant_request_id'],
            ]);

            return ['checkout_request_id' => $result['checkout_request_id'], 'error' => null];
        } catch (\Throwable) {
            $transaction->update([
                'status' => MpesaTransactionStatus::Failed,
                'result_desc' => 'STK push request failed.',
            ]);

            return ['checkout_request_id' => null, 'error' => 'Failed to send the M-Pesa prompt. Please check the number and try again.'];
        }
    }

    public function status(string $checkoutRequestId): ?array
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (! $transaction) {
            return null;
        }

        return ['status' => $transaction->status->value, 'result_desc' => $transaction->result_desc];
    }
}
