<?php

namespace Modules\Booking\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Explicit allow-list — Booking carries id_number (encrypted PII),
 * admin_notes, rejection_reason, and approved_by, none of which should ever
 * reach a JSON response. Never swap this for $booking->toArray().
 *
 * @property \Modules\Booking\Models\Booking $resource
 */
class BookingResource extends JsonResource
{
    /**
     * @param  array<string, array>  $programsById  Optional id => program array map (see
     *                                               BookingController::index()'s batch lookup, mirroring
     *                                               StudentDashboard's N+1 fix) — empty when not supplied.
     */
    public function __construct($resource, private readonly array $programsById = [])
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'program_id' => $this->program_id,
            'program_title' => $this->programsById[$this->program_id]['title'] ?? null,
            'cohort_id' => $this->cohort_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status->value,
            'payment_status' => $this->payment_status->value,
            'payment_method' => $this->payment_method,
            'amount_paid' => $this->amount_paid,
            'total_amount' => $this->total_amount,
            'currency' => $this->currency,
            'confirmed_at' => $this->confirmed_at,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
        ];
    }
}
