<?php

namespace Modules\Cms\Services;

use Modules\Cms\Models\Purchase;
use Modules\Cms\Models\Resource;
use Modules\Payment\Contracts\MpesaPaymentContract;

/**
 * Shared by the public ResourcePurchaseDialog Livewire component and the
 * API's ResourceController::purchase(), so the two client types can never
 * diverge on what "buying a resource" actually does. Extracted verbatim
 * from ResourcePurchaseDialog::payNow() — no behavior change.
 */
class ResourcePurchaseService
{
    public function __construct(private readonly MpesaPaymentContract $mpesa) {}

    /**
     * @return array{purchase_id: string, already_purchased: bool, checkout_request_id: ?string, error: ?string}
     */
    public function initiate(string $resourceId, string $userId, string $phone): array
    {
        $resource = Resource::findOrFail($resourceId);

        $purchase = Purchase::firstOrCreate(
            ['user_id' => $userId, 'resource_id' => $resource->id],
            ['amount' => $resource->price, 'currency' => $resource->currency, 'status' => 'pending'],
        );

        if ($purchase->status === 'paid') {
            return [
                'purchase_id' => $purchase->id,
                'already_purchased' => true,
                'checkout_request_id' => null,
                'error' => null,
            ];
        }

        $result = $this->mpesa->initiateForPurchase(
            purchaseId: $purchase->id,
            phone: $phone,
            amount: (string) $resource->price,
            accountReference: 'RES-'.strtoupper(substr($resource->id, 0, 8)),
            description: 'Kazilink Resource: '.$resource->title,
        );

        return [
            'purchase_id' => $purchase->id,
            'already_purchased' => false,
            'checkout_request_id' => $result['checkout_request_id'],
            'error' => $result['error'],
        ];
    }
}
