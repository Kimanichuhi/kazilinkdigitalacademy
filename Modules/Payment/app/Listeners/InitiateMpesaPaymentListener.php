<?php

namespace Modules\Payment\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Events\BookingCreated;
use Modules\Payment\Models\MpesaTransaction;
use Modules\Payment\Services\DarajaClient;

/**
 * Queued so a Daraja outage can never fail the booking submission itself
 * (ISOLATION RULES). Always records a pending transaction row; the STK push
 * itself is best-effort — if credentials aren't configured yet, or the
 * Safaricom call fails, the row is simply left `pending` for later manual
 * reconciliation.
 *
 * This is now a fallback only: the booking wizard's Payment step creates
 * the transaction and triggers the STK push itself (synchronously, via
 * MpesaPaymentContract) so the customer sees a live status instead of
 * waiting on a queued job. If a transaction already exists for this
 * booking, that already happened — this listener must not push a second
 * time.
 */
class InitiateMpesaPaymentListener implements ShouldQueue
{
    /**
     * Queued listeners are invoked positionally (`$handler->handle(...$data)`,
     * see Illuminate\Events\CallQueuedListener::handle()) — not through the
     * container's method-injection like a controller action — so extra
     * dependencies must be constructor-injected instead of added as a second
     * `handle()` parameter.
     */
    public function __construct(private readonly DarajaClient $daraja) {}

    public function handle(BookingCreated $event): void
    {
        if ($event->paymentMethod !== 'mpesa' || $event->paymentReference) {
            return;
        }

        if (MpesaTransaction::where('booking_id', $event->bookingId)->exists()) {
            return;
        }

        try {
            $transaction = MpesaTransaction::create([
                'booking_id' => $event->bookingId,
                'phone' => $event->phone,
                'amount' => $event->totalAmount ?? 0,
                'status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to record pending M-Pesa transaction for booking '.$event->bookingNumber, [
                'exception' => $e->getMessage(),
            ]);

            return;
        }

        if (! $this->daraja->configured()) {
            Log::info('M-Pesa Daraja credentials not configured; leaving transaction pending for manual reconciliation.', [
                'booking_number' => $event->bookingNumber,
            ]);

            return;
        }

        try {
            $result = $this->daraja->stkPush(
                phone: $event->phone,
                amount: (string) ($event->totalAmount ?? 0),
                accountReference: $event->bookingNumber,
                description: 'Kazilink Academy Booking '.$event->bookingNumber,
                callbackUrl: (string) config('payment.mpesa.callback_url'),
            );

            $transaction->update([
                'checkout_request_id' => $result['checkout_request_id'],
                'merchant_request_id' => $result['merchant_request_id'],
            ]);
        } catch (\Throwable $e) {
            Log::warning('STK push request failed for booking '.$event->bookingNumber, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
