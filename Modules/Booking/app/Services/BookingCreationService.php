<?php

namespace Modules\Booking\Services;

use Modules\Booking\Enums\BookingStatus;
use Modules\Booking\Enums\PaymentStatus;
use Modules\Booking\Events\BookingCreated;
use Modules\Booking\Models\Booking;

/**
 * Shared by the public booking wizard (Modules\Booking\Livewire\BookingWizard)
 * and the API's BookingController, so the two client types can never diverge
 * on what a "created"/"finalized" booking actually looks like. Extracted
 * verbatim from the wizard's continueFromDetails()/submit() methods — no
 * behavior change from the pre-extraction logic.
 */
class BookingCreationService
{
    /**
     * @param  array  $data  Same shape as BookingWizard::bookingFields() plus
     *                       'payment_method': program_id, cohort_id,
     *                       full_name, email, phone, id_number,
     *                       date_of_birth, gender, nationality, address,
     *                       county, constituency, current_occupation,
     *                       employer, education_level, referral_source,
     *                       emergency_contact_name, emergency_contact_phone,
     *                       special_requirements, consent_given, user_id,
     *                       total_amount, currency, payment_method.
     */
    public function create(array $data): Booking
    {
        return Booking::create($data + [
            'payment_reference' => null,
            'status' => BookingStatus::AwaitingPayment,
            'payment_status' => PaymentStatus::Pending,
        ]);
    }

    /**
     * @param  array  $data  Fields to apply on top of the booking's current
     *                       values. 'payment_method'/'payment_reference' are
     *                       silently dropped if the booking is already
     *                       Paid (e.g. a successful M-Pesa push already set
     *                       them) so a later finalize() call can never
     *                       clobber a completed payment.
     */
    public function finalize(Booking $booking, array $data): Booking
    {
        $updates = $data;

        if ($booking->payment_status === PaymentStatus::Paid) {
            unset($updates['payment_method'], $updates['payment_reference']);
        }

        $booking->update($updates);

        BookingCreated::dispatch($booking);

        return $booking;
    }
}
