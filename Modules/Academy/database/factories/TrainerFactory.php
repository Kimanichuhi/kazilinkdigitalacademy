<?php

namespace Modules\Academy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Academy\Models\Trainer;

/**
 * @extends Factory<Trainer>
 */
class TrainerFactory extends Factory
{
    protected $model = Trainer::class;

    public function definition(): array
    {
        return [
            'profile_id' => null,
            'full_name' => fake()->name(),
            'title' => fake()->randomElement(['Lead Trainer', 'Senior Instructor', 'Program Facilitator']),
            'bio' => fake()->paragraph(),
            'avatar_url' => null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+2547########'),
            'specializations' => fake()->randomElements(
                ['Freelancing', 'E-commerce', 'SEO', 'Copywriting', 'Web Development', 'Design'],
                2
            ),
            'social_links' => [],
            'rating' => fake()->randomFloat(2, 4, 5),
            'review_count' => fake()->numberBetween(0, 100),
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
            'order_index' => fake()->numberBetween(0, 20),
        ];
    }
}
