<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cms\Models\Faq;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['General', 'Payments', 'Programs', 'Certification']),
            'question' => fake()->sentence().'?',
            'answer' => fake()->paragraph(),
            'order_index' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }
}
