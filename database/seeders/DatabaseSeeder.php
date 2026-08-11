<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academy\Database\Seeders\AcademyDatabaseSeeder;
use Modules\Booking\Database\Seeders\BookingDatabaseSeeder;
use Modules\Cms\Database\Seeders\CmsDatabaseSeeder;
use Modules\Marketing\Database\Seeders\MarketingDatabaseSeeder;
use Modules\Notification\Database\Seeders\NotificationDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database. Order matters: User creates the 3
     * roles + one demo user per role before any other module seeds data
     * that references a user (Booking, Notification); Academy must run
     * before Cms so CmsDatabaseSeeder::seedSuccessStories() can link
     * testimonials to real programs by title.
     */
    public function run(): void
    {
        $this->call([
            UserDatabaseSeeder::class,
            AcademyDatabaseSeeder::class,
            BookingDatabaseSeeder::class,
            CmsDatabaseSeeder::class,
            MarketingDatabaseSeeder::class,
            NotificationDatabaseSeeder::class,
        ]);
    }
}
