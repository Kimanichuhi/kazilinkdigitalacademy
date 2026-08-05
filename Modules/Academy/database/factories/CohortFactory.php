<?php

namespace Modules\Academy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academy\Models\Cohort;
use Modules\Academy\Models\Program;
use Modules\Academy\Models\Trainer;

/**
 * @extends Factory<Cohort>
 */
class CohortFactory extends Factory
{
    protected $model = Cohort::class;

    public function definition(): array
    {
        $totalSeats = fake()->numberBetween(20, 50);

        return [
            'program_id' => Program::factory(),
            'trainer_id' => Trainer::factory(),
            'name' => 'Cohort '.fake()->monthName().' '.fake()->year(),
            'start_date' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'end_date' => fake()->dateTimeBetween('+3 months', '+4 months'),
            'registration_deadline' => fake()->dateTimeBetween('now', '+1 week'),
            'total_seats' => $totalSeats,
            'booked_seats' => fake()->numberBetween(0, $totalSeats),
            'schedule_details' => 'Mon, Wed, Fri — 6:00 PM to 8:00 PM',
            'schedule_json' => [],
            'venue' => null,
            'venue_address' => null,
            'online_link' => null,
            'online_platform' => 'Zoom',
            'price' => null,
            'currency' => 'KES',
            'status' => fake()->randomElement(['upcoming', 'open']),
            'is_featured' => false,
            'notes' => null,
        ];
    }
}
