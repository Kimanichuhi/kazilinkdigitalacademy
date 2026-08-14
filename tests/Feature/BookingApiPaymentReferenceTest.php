<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Booking\Models\Booking;
use Tests\TestCase;

class BookingApiPaymentReferenceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression test: `payment_reference` used to be validated (min:6) on
     * the raw input, then whitespace-stripped afterward — so a value that
     * was only long enough because of internal whitespace (e.g. "A     ",
     * 6 chars) passed validation and then collapsed to "A" once stored.
     * Normalization must happen before validation.
     */
    public function test_reference_that_is_only_long_enough_via_whitespace_is_rejected(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'mpesa',
            'payment_reference' => null,
        ]);

        $response = $this->actingAs($user)->postJson("/api/v1/bookings/{$booking->id}/confirm", [
            'consent_given' => true,
            'payment_reference' => 'A     ',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payment_reference');

        $this->assertNull($booking->fresh()->payment_reference);
    }

    public function test_reference_is_normalized_to_uppercase_without_whitespace(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'payment_method' => 'mpesa',
            'payment_reference' => null,
        ]);

        $response = $this->actingAs($user)->postJson("/api/v1/bookings/{$booking->id}/confirm", [
            'consent_given' => true,
            'payment_reference' => '  fsc6afdcs  ',
        ]);

        $response->assertOk();
        $this->assertSame('FSC6AFDCS', $booking->fresh()->payment_reference);
    }
}
