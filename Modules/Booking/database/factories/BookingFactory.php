<?php

namespace Modules\Booking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academy\Models\Program;
use Modules\Booking\Enums\BookingStatus;
use Modules\Booking\Enums\PaymentStatus;
use Modules\Booking\Models\Booking;

/**
 * @extends Factory<Booking>
 *
 * References Modules\Academy\Models\Program directly for demo-data wiring
 * only — seeders/factories are outside the runtime cross-module isolation
 * boundary the app code (Controllers/Services/Policies) must respect.
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $amount = fake()->randomElement([9999, 14999, 19999, 24999]);

        return [
            'user_id' => null,
            'program_id' => Program::factory(),
            'cohort_id' => null,
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+2547########'),
            'id_number' => fake()->numerify('########'),
            'date_of_birth' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female', 'prefer_not']),
            'nationality' => 'Kenyan',
            'address' => fake()->streetAddress(),
            'county' => 'Nairobi',
            'constituency' => 'Westlands',
            'current_occupation' => fake()->jobTitle(),
            'employer' => fake()->company(),
            'education_level' => fake()->randomElement(['secondary', 'certificate', 'diploma', 'degree', 'postgraduate']),
            'payment_method' => 'mpesa',
            'payment_reference' => null,
            'amount_paid' => 0,
            'total_amount' => $amount,
            'currency' => 'KES',
            'payment_status' => PaymentStatus::Pending,
            'status' => BookingStatus::Draft,
            'referral_source' => fake()->randomElement(['facebook', 'instagram', 'google', 'friend']),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->numerify('+2547########'),
            'special_requirements' => null,
            'documents_urls' => [],
            'admin_notes' => null,
            'rejection_reason' => null,
            'consent_given' => true,
        ];
    }

    public function awaitingPayment(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::AwaitingPayment,
            'payment_reference' => strtoupper(fake()->bothify('??######')),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => BookingStatus::Paid,
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => strtoupper(fake()->bothify('??######')),
            'amount_paid' => $attrs['total_amount'],
        ]);
    }

    public function pendingApproval(): static
    {
        return $this->paid()->state(fn () => [
            'status' => BookingStatus::PendingApproval,
        ]);
    }

    public function approved(): static
    {
        return $this->pendingApproval()->state(fn () => [
            'status' => BookingStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->pendingApproval()->state(fn () => [
            'status' => BookingStatus::Rejected,
            'rejection_reason' => 'Payment reference could not be verified.',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => BookingStatus::Cancelled,
        ]);
    }

    public function completed(): static
    {
        return $this->approved()->state(fn () => [
            'status' => BookingStatus::Completed,
        ]);
    }
}
