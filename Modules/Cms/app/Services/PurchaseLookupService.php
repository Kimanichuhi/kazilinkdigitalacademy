<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\PurchaseLookupContract;
use Modules\Cms\Models\Purchase;

class PurchaseLookupService implements PurchaseLookupContract
{
    public function markPaid(string $id, ?string $paymentId, string $amountPaid): void
    {
        $purchase = Purchase::find($id);

        if (! $purchase || $purchase->status === 'paid') {
            return;
        }

        $purchase->update([
            'status' => 'paid',
            'payment_id' => $paymentId ?: $purchase->payment_id,
            'purchased_at' => now(),
        ]);
    }

    public function markPaymentFailed(string $id, ?string $reason = null): void
    {
        $purchase = Purchase::find($id);

        if (! $purchase || $purchase->status === 'paid') {
            return;
        }

        $purchase->update(['status' => 'failed']);
    }
}
