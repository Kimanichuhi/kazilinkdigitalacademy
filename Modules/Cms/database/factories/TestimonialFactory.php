<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cms\Models\Testimonial;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'program_id' => null,
            'student_name' => fake()->name(),
            'student_title' => fake()->jobTitle(),
            'student_avatar_url' => null,
            'content' => fake()->paragraph(),
            'rating' => fake()->randomFloat(1, 4, 5),
            'income_before' => 'KES '.fake()->numberBetween(0, 20000),
            'income_after' => 'KES '.fake()->numberBetween(30000, 150000),
            'video_url' => null,
            'is_featured' => fake()->boolean(30),
            'is_published' => true,
            'order_index' => fake()->numberBetween(0, 20),
        ];
    }
}
