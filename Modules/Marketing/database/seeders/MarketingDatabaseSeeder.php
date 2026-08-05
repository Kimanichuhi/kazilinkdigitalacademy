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
        Cta::factory()->create();

        $stats = [
            'students_trained' => ['label' => 'Students Trained', 'value' => '2,000+', 'icon' => 'Users', 'order_index' => 1],
            'programs_offered' => ['label' => 'Programs Offered', 'value' => '12+', 'icon' => 'BookOpen', 'order_index' => 2],
            'success_rate' => ['label' => 'Success Rate', 'value' => '94%', 'icon' => 'TrendingUp', 'order_index' => 3],
            'countries_reached' => ['label' => 'Countries Reached', 'value' => '5', 'icon' => 'Globe', 'order_index' => 4],
            'years_of_excellence' => ['label' => 'Years of Excellence', 'value' => '6', 'icon' => 'Award', 'order_index' => 5],
            'avg_income_increase' => ['label' => 'Average Income Increase', 'value' => '3x', 'icon' => 'DollarSign', 'order_index' => 6],
            'avg_rating' => ['label' => 'Average Rating', 'value' => '4.8/5', 'icon' => 'Star', 'description' => 'Average student rating across testimonials', 'order_index' => 7],
        ];

        foreach ($stats as $key => $attrs) {
            Statistic::firstOrCreate(['key' => $key], [...$attrs, 'is_active' => true]);
        }
    }
}
