<?php

namespace Modules\Booking\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Academy\Models\Program;
use Modules\Booking\Models\Booking;

class BookingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds. Creates one booking per lifecycle state so
     * every admin bookings-table status filter has at least one row.
     */
    public function run(): void
    {
        $program = Program::query()->inRandomOrder()->first() ?? Program::factory()->create();
        $student = User::role('student')->first();

        Booking::factory()->create(['program_id' => $program->id]);
        Booking::factory()->awaitingPayment()->create(['program_id' => $program->id]);
        Booking::factory()->paid()->create(['program_id' => $program->id]);
        Booking::factory()->pendingApproval()->create(['program_id' => $program->id]);
        Booking::factory()->approved()->create([
            'program_id' => $program->id,
            'user_id' => $student?->id,
        ]);
        Booking::factory()->rejected()->create(['program_id' => $program->id]);
        Booking::factory()->cancelled()->create(['program_id' => $program->id]);
        Booking::factory()->completed()->create([
            'program_id' => $program->id,
            'user_id' => $student?->id,
        ]);
    }
}
