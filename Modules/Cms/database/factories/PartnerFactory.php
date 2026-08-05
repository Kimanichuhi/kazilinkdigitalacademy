<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cms\Models\Partner;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'logo_url' => null,
            'website_url' => fake()->url(),
            'description' => fake()->sentence(),
            'is_active' => true,
            'order_index' => fake()->numberBetween(0, 20),
        ];
    }
}
