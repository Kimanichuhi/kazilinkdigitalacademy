<?php

namespace Modules\Notification\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Notification\Models\Notification;

class NotificationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::role('student')->first();

        if ($student) {
            Notification::factory()->create([
                'user_id' => $student->id,
                'title' => 'Welcome to Kazilink Digital Academy',
                'message' => 'Explore our programs and book your first training session.',
                'type' => 'info',
                'link' => '/programs',
            ]);
        }
    }
}
