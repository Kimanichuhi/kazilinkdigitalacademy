<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Cms\Models\BlogCategory;

/**
 * @extends Factory<BlogCategory>
 */
class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Success Stories', 'Freelancing Tips', 'Industry News', 'Tutorials']);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->sentence(),
            'color' => fake()->hexColor(),
            'order_index' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
