<?php

namespace Modules\Booking\Support;

/**
 * The Ol Kalou Special Offer: a flat 15% discount on any program for
 * applicants whose constituency is Ol Kalou. Centralized here so the public
 * wizard and the mobile API (Modules\Booking\Http\Controllers\Api\BookingController)
 * can never compute two different discounted prices for the same booking —
 * both feed the result into `total_amount`, which is the only figure
 * Modules\Payment\Services\MpesaPaymentService::initiateForBooking() trusts
 * when it re-derives the actual M-Pesa charge.
 *
 * Eligibility is based solely on the applicant's self-reported constituency
 * at booking time — the discount applies immediately, it is not gated on
 * uploading an ID. The optional ID upload (BookingWizard::idDocumentPhoto)
 * exists only so staff can verify the claim afterward; a false constituency
 * claim is grounds to revoke the discount and recover the difference, not a
 * precondition for granting it.
 */
class OlKalouOffer
{
    public const CONSTITUENCY = 'Ol Kalou';

    public const DISCOUNT_RATE = 0.15;

    public static function isEligible(?string $constituency): bool
    {
        return $constituency === self::CONSTITUENCY;
    }

    public static function apply(?float $price, ?string $constituency): ?float
    {
        if ($price === null) {
            return null;
        }

        return self::isEligible($constituency) ? round($price * (1 - self::DISCOUNT_RATE), 2) : $price;
    }
}
