<?php

namespace Modules\Academy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Academy\Models\Program;
use Modules\Academy\Models\ProgramCategory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();
        $price = fake()->randomElement([9999, 14999, 19999, 24999, 34999]);

        return [
            'category_id' => ProgramCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'subtitle' => fake()->sentence(6),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(12),
            'thumbnail_url' => null,
            'gallery_urls' => [],
            'duration_weeks' => fake()->numberBetween(4, 16),
            'duration_label' => fake()->numberBetween(4, 16).' Weeks',
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'delivery_mode' => fake()->randomElement(['online', 'hybrid', 'in_person']),
            'price' => $price,
            'original_price' => $price + 5000,
            'currency' => 'KES',
            'is_featured' => fake()->boolean(30),
            'is_active' => true,
            'is_published' => true,
            'rating' => fake()->randomFloat(2, 4, 5),
            'review_count' => fake()->numberBetween(0, 200),
            'enrollment_count' => fake()->numberBetween(0, 2000),
            'curriculum' => [],
            'outcomes' => [fake()->sentence(), fake()->sentence(), fake()->sentence()],
            'requirements' => [fake()->sentence()],
            'seo_title' => $title,
            'seo_description' => fake()->sentence(15),
            'seo_keywords' => implode(', ', fake()->words(5)),
            'order_index' => fake()->numberBetween(0, 20),
        ];
    }
}
