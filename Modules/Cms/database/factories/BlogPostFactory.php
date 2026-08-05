<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Models\BlogPost;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'category_id' => BlogCategory::factory(),
            'author_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'excerpt' => fake()->sentence(20),
            'content' => fake()->paragraphs(6, true),
            'thumbnail_url' => null,
            'tags' => fake()->randomElements(['freelancing', 'ecommerce', 'income', 'skills'], 2),
            'is_featured' => fake()->boolean(20),
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(0, 60)),
            'view_count' => fake()->numberBetween(0, 5000),
            'seo_title' => $title,
            'seo_description' => fake()->sentence(15),
            'seo_keywords' => implode(', ', fake()->words(5)),
            'read_time_minutes' => fake()->numberBetween(3, 12),
        ];
    }
}
