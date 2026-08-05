<?php

namespace Modules\Academy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Academy\Models\ProgramCategory;

/**
 * @extends Factory<ProgramCategory>
 */
class ProgramCategoryFactory extends Factory
{
    protected $model = ProgramCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Freelancing', 'E-commerce', 'Digital Marketing', 'Graphic Design',
            'Virtual Assistance', 'Content Writing', 'Web Development', 'Data Entry',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->sentence(),
            'icon' => 'Layers',
            'color' => fake()->hexColor(),
            'order_index' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
