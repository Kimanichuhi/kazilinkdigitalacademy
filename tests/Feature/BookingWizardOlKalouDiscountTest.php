<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Academy\Models\Program;
use Modules\Booking\Livewire\BookingWizard;
use Modules\Booking\Models\Booking;
use Tests\TestCase;

/**
 * The Ol Kalou Special Offer is a flat 15% discount, applied automatically
 * (no ID/approval required) purely on the applicant's selected constituency
 * — see Modules\Booking\Support\OlKalouOffer.
 */
class BookingWizardOlKalouDiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_ol_kalou_constituency_gets_15_percent_off_the_stored_total(): void
    {
        $program = Program::factory()->create(['price' => 10000, 'is_active' => true, 'is_published' => true]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 2)
            ->set('full_name', 'Jane Wanjiku')
            ->set('email', 'jane@example.com')
            ->set('phone', '0712345678')
            ->set('county', 'Nyandarua')
            ->set('constituency', 'Ol Kalou')
            ->call('continueFromDetails')
            ->assertHasNoErrors();

        $booking = Booking::where('email', 'jane@example.com')->latest('created_at')->first();

        $this->assertNotNull($booking);
        $this->assertEquals(8500.0, (float) $booking->total_amount);
    }

    public function test_other_constituencies_pay_the_standard_price(): void
    {
        $program = Program::factory()->create(['price' => 10000, 'is_active' => true, 'is_published' => true]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 2)
            ->set('full_name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('phone', '0712345678')
            ->set('county', 'Nairobi')
            ->set('constituency', 'Westlands')
            ->call('continueFromDetails')
            ->assertHasNoErrors();

        $booking = Booking::where('email', 'john@example.com')->latest('created_at')->first();

        $this->assertNotNull($booking);
        $this->assertEquals(10000.0, (float) $booking->total_amount);
    }

    public function test_payment_step_shows_the_discounted_total_and_original_price(): void
    {
        $program = Program::factory()->create(['price' => 10000, 'currency' => 'KES', 'is_active' => true, 'is_published' => true]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 3)
            ->set('constituency', 'Ol Kalou')
            ->assertSee('8,500')
            ->assertSee('10,000')
            ->assertSee('15% off applied');
    }

    public function test_api_booking_store_applies_the_ol_kalou_discount(): void
    {
        $user = User::factory()->create();
        $program = Program::factory()->create(['price' => 10000, 'is_active' => true, 'is_published' => true]);

        $response = $this->actingAs($user)->postJson('/api/v1/bookings', [
            'program_id' => $program->id,
            'full_name' => 'Jane Wanjiku',
            'email' => 'jane.api@example.com',
            'phone' => '0712345678',
            'county' => 'Nyandarua',
            'constituency' => 'Ol Kalou',
            'payment_method' => 'bank',
        ]);

        $response->assertCreated();
        $this->assertEquals(8500.0, (float) $response->json('data.total_amount'));
    }
}
