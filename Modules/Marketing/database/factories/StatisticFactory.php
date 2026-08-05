<?php

namespace Modules\Marketing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Marketing\Models\Statistic;

/**
 * @extends Factory<Statistic>
 */
class StatisticFactory extends Factory
{
    protected $model = Statistic::class;

    public function definition(): array
    {
        return [
            'label' => fake()->words(2, true),
            'value' => fake()->numberBetween(10, 5000).'+',
            'icon' => 'Star',
            'description' => fake()->sentence(),
            'order_index' => fake()->numberBetween(0, 10),
            'is_active' => true,
            'key' => null,
        ];
    }
}
