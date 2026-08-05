<?php

namespace Modules\Payment\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Contracts\BookingLookupContract;
use Modules\Cms\Contracts\PurchaseLookupContract;
use Modules\Payment\Enums\MpesaTransactionStatus;
use Modules\Payment\Models\MpesaTransaction;
use Modules\Payment\Services\DarajaClient;

/**
 * Shared hosting has no queue worker daemon or websockets, so a callback
 * that Safaricom fails to deliver (or that never reaches a local/dev box
 * without a public URL) would otherwise leave a transaction pending
 * forever. This command is intended to run every few minutes via the cron
 * `schedule:run` line (see PaymentServiceProvider::configureSchedules) to
 * actively query Daraja for anything still unresolved.
 */
class ReconcileMpesaTransactions extends Command
{
    protected $signature = 'payment:reconcile-mpesa';

    protected $description = 'Query Safaricom for the status of pending M-Pesa STK push transactions whose callback has not yet arrived.';

    public function handle(DarajaClient $daraja, BookingLookupContract $bookings, PurchaseLookupContract $purchases): int
    {
        if (! $daraja->configured()) {
            $this->info('M-Pesa credentials not configured; skipping reconciliation.');

            return self::SUCCESS;
        }

        $pending = MpesaTransaction::query()
            ->where('status', MpesaTransactionStatus::Pending)
            ->whereNotNull('checkout_request_id')
            ->where('created_at', '<=', now()->subMinutes(2))
            ->where('created_at', '>=', now()->subHours(6))
            ->get();

        foreach ($pending as $transaction) {
            try {
                $result = $daraja->stkQuery($transaction->checkout_request_id);
                $resultCode = $result['ResultCode'] ?? null;

                if ($resultCode === null) {
                    continue;
                }

                $resultCode = (int) $resultCode;

                if ($resultCode === 0) {
                    $transaction->update([
                        'status' => MpesaTransactionStatus::Success,
                        'result_code' => (string) $resultCode,
                        'result_desc' => $result['ResultDesc'] ?? null,
                        'raw_callback_payload' => $result,
                    ]);

                    if ($transaction->booking_id) {
                        $bookings->markPaid($transaction->booking_id, null, (string) $transaction->amount);
                    } elseif ($transaction->purchase_id) {
                        $purchases->markPaid($transaction->purchase_id, $transaction->id, (string) $transaction->amount);
                    }
                } else {
                    $transaction->update([
                        'status' => $resultCode === 1032 ? MpesaTransactionStatus::Cancelled : MpesaTransactionStatus::Failed,
                        'result_code' => (string) $resultCode,
                        'result_desc' => $result['ResultDesc'] ?? null,
                        'raw_callback_payload' => $result,
                    ]);

                    if ($transaction->booking_id) {
                        $bookings->markPaymentFailed($transaction->booking_id, $result['ResultDesc'] ?? null);
                    } elseif ($transaction->purchase_id) {
                        $purchases->markPaymentFailed($transaction->purchase_id, $result['ResultDesc'] ?? null);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('M-Pesa reconciliation query failed', [
                    'transaction_id' => $transaction->id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        MpesaTransaction::query()
            ->where('status', MpesaTransactionStatus::Pending)
            ->where('created_at', '<', now()->subHours(6))
            ->update(['status' => MpesaTransactionStatus::Timeout]);

        return self::SUCCESS;
    }
}
