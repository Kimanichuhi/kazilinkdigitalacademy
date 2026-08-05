<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cms\Models\Resource;

/**
 * @extends Factory<Resource>
 */
class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        return [
            'program_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(15),
            'type' => fake()->randomElement(['pdf', 'video', 'template', 'checklist']),
            'file_url' => null,
            'thumbnail_url' => null,
            'is_free' => fake()->boolean(70),
            'is_published' => true,
            'download_count' => fake()->numberBetween(0, 1000),
            'tags' => fake()->randomElements(['beginner', 'template', 'guide'], 2),
            'order_index' => fake()->numberBetween(0, 20),
        ];
    }
}
