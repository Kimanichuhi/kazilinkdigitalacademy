<?php

namespace Modules\Cms\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Cms\Models\Purchase;
use Modules\Cms\Models\Resource;
use Modules\Cms\Services\ResourcePurchaseService;

/**
 * Owns the entire "download this resource" action area for a single
 * resource card: shows a plain Download link for free/already-purchased
 * resources, otherwise a Buy button that opens an inline M-Pesa purchase
 * flow (phone input + Pay Now + live status, reusing Payment's poller
 * exactly the way BookingWizard does).
 */
class ResourcePurchaseDialog extends Component
{
    public string $resourceId;

    public bool $open = false;

    public string $mpesaPhone = '';

    public bool $mpesaPushing = false;

    public ?string $mpesaError = null;

    public ?string $mpesaCheckoutRequestId = null;

    public ?string $purchaseId = null;

    public function mount(string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function openDialog(): void
    {
        if (! auth()->check()) {
            $this->mpesaError = 'Please log in to purchase this resource.';
            $this->open = true;

            return;
        }

        $this->open = true;
        $this->mpesaError = null;
    }

    public function closeDialog(): void
    {
        $this->open = false;
    }

    public function payNow(ResourcePurchaseService $purchases): void
    {
        if (! auth()->check()) {
            $this->mpesaError = 'Please log in to purchase this resource.';

            return;
        }

        $this->validate([
            'mpesaPhone' => 'required|string|min:9',
        ], [
            'mpesaPhone.required' => 'Valid M-Pesa phone number required',
            'mpesaPhone.min' => 'Valid M-Pesa phone number required',
        ]);

        $this->mpesaError = null;
        $this->mpesaPushing = true;

        $result = $purchases->initiate($this->resourceId, auth()->id(), $this->mpesaPhone);

        $this->purchaseId = $result['purchase_id'];

        if ($result['already_purchased']) {
            $this->open = false;
            $this->mpesaPushing = false;

            return;
        }

        $this->mpesaCheckoutRequestId = $result['checkout_request_id'];
        $this->mpesaError = $result['error'];
        $this->mpesaPushing = false;
    }

    #[On('mpesa-payment-succeeded')]
    public function onMpesaPaymentSucceeded(): void
    {
        $this->mpesaCheckoutRequestId = null;
        $this->open = false;
    }

    #[On('mpesa-payment-failed')]
    public function onMpesaPaymentFailed(?string $reason = null): void
    {
        $this->mpesaError = $reason ?: 'Payment failed. Please try again.';
        $this->mpesaCheckoutRequestId = null;
    }

    public function render()
    {
        $resource = Resource::findOrFail($this->resourceId);

        $hasAccess = ! $resource->is_paid || (auth()->check() && Purchase::query()
            ->where('resource_id', $resource->id)
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->exists());

        return view('cms::livewire.resource-purchase-dialog', [
            'resource' => $resource,
            'hasAccess' => $hasAccess,
        ]);
    }
}
