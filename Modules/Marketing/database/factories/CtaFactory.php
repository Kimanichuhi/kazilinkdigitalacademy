<?php

namespace Modules\Marketing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Marketing\Models\Cta;

/**
 * @extends Factory<Cta>
 */
class CtaFactory extends Factory
{
    protected $model = Cta::class;

    public function definition(): array
    {
        return [
            'name' => 'homepage-bottom',
            'title' => 'Start Earning Online Today',
            'subtitle' => 'Join thousands of Kenyans building income skills',
            'description' => fake()->sentence(15),
            'background_color' => null,
            'background_image_url' => null,
            'button_one_text' => 'Browse Programs',
            'button_one_link' => '/programs',
            'button_one_style' => 'primary',
            'button_two_text' => 'Book Now',
            'button_two_link' => '/booking',
            'button_two_style' => 'outline',
            'placement' => ['home'],
            'priority' => fake()->numberBetween(0, 10),
            'is_active' => true,
            'publish_date' => null,
            'expiry_date' => null,
        ];
    }
}
