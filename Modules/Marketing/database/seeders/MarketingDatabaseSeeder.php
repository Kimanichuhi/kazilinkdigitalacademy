<?php

namespace Modules\Marketing\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Marketing\Models\Advertisement;
use Modules\Marketing\Models\Cta;
use Modules\Marketing\Models\Statistic;

class MarketingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds. Statistic `key` values match the source's
     * migration 20260730140000_add_statistics_key.sql exactly so the
     * eventual home/about/success-stories views can look them up by slug.
     */
    public function run(): void
    {
        Advertisement::factory()->create();

        Cta::updateOrCreate(
            ['name' => 'homepage-bottom'],
            [
                'title' => 'Start Earning Online Today',
                'subtitle' => 'Join thousands of Kenyans building income skills',
                'description' => 'Enroll in a KAZI Link Academy course and start your journey to a real, income-generating digital skill.',
                'button_one_text' => 'Enroll Now',
                'button_one_link' => '/register',
                'button_one_style' => 'primary',
                'button_two_text' => 'Browse Courses',
                'button_two_link' => '/programs',
                'button_two_style' => 'secondary',
                'placement' => ['homepage'],
                'priority' => 10,
                'is_active' => true,
            ]
        );

        // Homepage STATISTICS section (brief, 2026-08-06).
        $stats = [
            'students_trained' => ['label' => 'Students Trained', 'value' => '5000+', 'icon' => 'Users', 'order_index' => 1],
            'programs_offered' => ['label' => 'Professional Courses', 'value' => '20+', 'icon' => 'BookOpen', 'order_index' => 2],
            'success_rate' => ['label' => 'Student Satisfaction', 'value' => '95%', 'icon' => 'TrendingUp', 'order_index' => 3],
            'avg_income_increase' => ['label' => 'Students Earning From Skills', 'value' => '70%+', 'icon' => 'DollarSign', 'order_index' => 4],
            'learning_access' => ['label' => 'Learning Access', 'value' => '24/7', 'icon' => 'Zap', 'order_index' => 5],
            // Success Stories page RESULTS COUNTER section — distinct numbers from the brief.
            'certificates_awarded' => ['label' => 'Certificates Awarded', 'value' => '4200+', 'icon' => 'Award', 'order_index' => 6],
            'freelancers_started' => ['label' => 'Freelancers Started', 'value' => '1800+', 'icon' => 'Globe', 'order_index' => 7],
        ];

        foreach ($stats as $key => $attrs) {
            Statistic::updateOrCreate(['key' => $key], [...$attrs, 'is_active' => true]);
        }

        // Retired keys no longer in the brief — deactivate rather than
        // delete, in case anything historical still references them.
        Statistic::whereIn('key', ['countries_reached', 'years_of_excellence', 'avg_rating'])
            ->update(['is_active' => false]);
    }
}
