<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cms\Models\TeamMember;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'title' => fake()->randomElement(['Chief Executive Officer', 'Head of Academics', 'Operations Manager']),
            'bio' => fake()->paragraph(),
            'avatar_url' => null,
            'email' => fake()->unique()->safeEmail(),
            'social_links' => [],
            'department' => fake()->randomElement(['Leadership', 'Academics', 'Operations']),
            'is_featured' => fake()->boolean(30),
            'is_active' => true,
            'order_index' => fake()->numberBetween(0, 20),
        ];
    }
}
