<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Contracts\BookingLookupContract;
use Modules\Cms\Contracts\PurchaseLookupContract;
use Modules\Payment\Enums\MpesaTransactionStatus;
use Modules\Payment\Models\MpesaTransaction;

/**
 * Safaricom posts here (unauthenticated, per Daraja's design — see
 * bootstrap/app.php's CSRF exception for this path). Always responds 200
 * with a valid ResultCode/ResultDesc body regardless of what we did
 * internally, otherwise Safaricom will retry indefinitely.
 */
class MpesaCallbackController extends Controller
{
    public function handle(Request $request, string $secret, BookingLookupContract $bookings, PurchaseLookupContract $purchases): JsonResponse
    {
        $expectedSecret = (string) config('payment.mpesa.callback_secret');

        // Fail closed: an unconfigured secret means this endpoint should
        // never accept anything, not fall back to trusting any request.
        if ($expectedSecret === '' || ! hash_equals($expectedSecret, $secret)) {
            Log::warning('M-Pesa callback rejected: invalid callback secret');

            abort(403);
        }

        $body = $request->input('Body.stkCallback', []);
        $checkoutRequestId = $body['CheckoutRequestID'] ?? null;

        if (! $checkoutRequestId) {
            // Deliberately not logging the payload — it can carry a phone
            // number even on this malformed-request path.
            Log::warning('M-Pesa callback missing CheckoutRequestID');

            return $this->accepted();
        }

        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (! $transaction) {
            Log::warning('M-Pesa callback for unknown CheckoutRequestID', ['checkout_request_id' => $checkoutRequestId]);

            return $this->accepted();
        }

        if ($transaction->status !== MpesaTransactionStatus::Pending) {
            // Already processed — Safaricom may retry the same callback.
            return $this->accepted();
        }

        $resultCode = (int) ($body['ResultCode'] ?? -1);
        $resultDesc = $body['ResultDesc'] ?? null;

        if ($resultCode === 0) {
            $items = collect($body['CallbackMetadata']['Item'] ?? [])
                ->filter(fn ($item) => isset($item['Name']))
                ->mapWithKeys(fn ($item) => [$item['Name'] => $item['Value'] ?? null]);

            $receiptNumber = $items->get('MpesaReceiptNumber');
            $amount = $items->get('Amount');
            $transactionDate = $items->get('TransactionDate');

            // Atomic: if markPaid() throws, the transaction row must stay
            // Pending (not Success) so a Safaricom retry — or the
            // reconciliation command — gets a real second chance instead of
            // hitting the "already processed" short-circuit above forever
            // while the booking is silently never marked paid.
            try {
                DB::transaction(function () use ($transaction, $resultCode, $resultDesc, $receiptNumber, $transactionDate, $body, $amount, $bookings, $purchases) {
                    $transaction->update([
                        'status' => MpesaTransactionStatus::Success,
                        'result_code' => (string) $resultCode,
                        'result_desc' => $resultDesc,
                        'mpesa_receipt_number' => $receiptNumber,
                        'transaction_date' => $transactionDate ? Carbon::createFromFormat('YmdHis', (string) $transactionDate) : null,
                        'raw_callback_payload' => $body,
                    ]);

                    if ($transaction->booking_id) {
                        $bookings->markPaid($transaction->booking_id, $receiptNumber, (string) ($amount ?? $transaction->amount));
                    } elseif ($transaction->purchase_id) {
                        $purchases->markPaid($transaction->purchase_id, $transaction->id, (string) ($amount ?? $transaction->amount));
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Failed to mark paid after M-Pesa callback', [
                    'booking_id' => $transaction->booking_id,
                    'purchase_id' => $transaction->purchase_id,
                    'exception' => $e->getMessage(),
                ]);
            }
        } else {
            try {
                DB::transaction(function () use ($transaction, $resultCode, $resultDesc, $body, $bookings, $purchases) {
                    $transaction->update([
                        'status' => $resultCode === 1032 ? MpesaTransactionStatus::Cancelled : MpesaTransactionStatus::Failed,
                        'result_code' => (string) $resultCode,
                        'result_desc' => $resultDesc,
                        'raw_callback_payload' => $body,
                    ]);

                    if ($transaction->booking_id) {
                        $bookings->markPaymentFailed($transaction->booking_id, $resultDesc);
                    } elseif ($transaction->purchase_id) {
                        $purchases->markPaymentFailed($transaction->purchase_id, $resultDesc);
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Failed to mark payment-failed after M-Pesa callback', [
                    'booking_id' => $transaction->booking_id,
                    'purchase_id' => $transaction->purchase_id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return $this->accepted();
    }

    protected function accepted(): JsonResponse
    {
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
