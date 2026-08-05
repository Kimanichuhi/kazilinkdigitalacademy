<?php

namespace Modules\Marketing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Marketing\Models\Advertisement;

/**
 * @extends Factory<Advertisement>
 */
class AdvertisementFactory extends Factory
{
    protected $model = Advertisement::class;

    public function definition(): array
    {
        return [
            'campaign_name' => fake()->catchPhrase(),
            'title' => 'Enrollment closes soon — limited seats available',
            'subtitle' => null,
            'description' => null,
            'type' => 'announcement_bar',
            'placement' => ['home'],
            'desktop_image_url' => null,
            'mobile_image_url' => null,
            'video_url' => null,
            'cta_text' => 'Enroll Now',
            'cta_link' => '/booking',
            'button_style' => null,
            'background_color' => null,
            'overlay_color' => null,
            'animation' => null,
            'priority' => fake()->numberBetween(0, 10),
            'target_audience' => [],
            'status' => 'active',
            'publish_date' => now()->subDay(),
            'expiry_date' => now()->addMonths(2),
            'view_count' => fake()->numberBetween(0, 5000),
            'click_count' => fake()->numberBetween(0, 500),
        ];
    }
}
