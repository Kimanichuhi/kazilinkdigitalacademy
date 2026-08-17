<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cms\Database\Factories\TestimonialFactory;
use Modules\Core\Models\BaseModel;

class Testimonial extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): TestimonialFactory
    {
        return TestimonialFactory::new();
    }

    const UPDATED_AT = null;

    protected $fillable = [
        'program_id', 'student_name', 'student_title', 'student_avatar_url', 'content',
        'location', 'course_completed', 'achievement',
        'rating', 'income_before', 'income_after', 'video_url', 'is_featured',
        'is_published', 'order_index',
    ];

    protected function casts(): array
    {
        return [
            'student_avatar_url' => \Modules\Core\Casts\StorageUrl::class,
            'rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
