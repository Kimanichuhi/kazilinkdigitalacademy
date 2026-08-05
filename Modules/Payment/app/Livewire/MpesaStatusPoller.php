<?php

namespace Modules\Payment\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Modules\Payment\Enums\MpesaTransactionStatus;
use Modules\Payment\Models\MpesaTransaction;

/**
 * Polls a single M-Pesa transaction until it reaches a terminal state, then
 * dispatches a browser/Livewire event so the embedding component (booking
 * wizard, resource purchase dialog) can react — this component never
 * assumes what "success" should trigger, keeping it reusable across both.
 */
class MpesaStatusPoller extends Component
{
    // Set once from the parent component's Blade tag and never bound to a
    // form input — locked so a crafted client update can't repoint this
    // poller at someone else's transaction to read its status.
    #[Locked]
    public string $checkoutRequestId;

    public bool $polling = true;

    public bool $justSent = true;

    public function poll(): void
    {
        if (! $this->polling) {
            return;
        }

        $transaction = MpesaTransaction::where('checkout_request_id', $this->checkoutRequestId)->first();

        if (! $transaction || $transaction->status === MpesaTransactionStatus::Pending) {
            $this->justSent = false;

            return;
        }

        $this->polling = false;

        match ($transaction->status) {
            MpesaTransactionStatus::Success => $this->dispatch('mpesa-payment-succeeded'),
            MpesaTransactionStatus::Cancelled => $this->dispatch('mpesa-payment-failed', reason: 'Payment was cancelled.'),
            default => $this->dispatch('mpesa-payment-failed', reason: $transaction->result_desc ?: 'Payment failed. Please try again.'),
        };
    }

    public function render()
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $this->checkoutRequestId)->first();

        return view('payment::livewire.mpesa-status-poller', [
            'status' => $transaction?->status ?? MpesaTransactionStatus::Pending,
        ]);
    }
}
