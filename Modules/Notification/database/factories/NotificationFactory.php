<?php

namespace Modules\Notification\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Notification\Models\Notification;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'title' => fake()->sentence(4),
            'message' => fake()->sentence(12),
            'type' => fake()->randomElement(['info', 'success', 'warning', 'error']),
            'link' => null,
            'is_read' => false,
        ];
    }
}
