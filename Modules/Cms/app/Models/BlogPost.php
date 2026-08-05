<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Cms\Database\Factories\BlogPostFactory;
use Modules\Core\Models\BaseModel;

class BlogPost extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): BlogPostFactory
    {
        return BlogPostFactory::new();
    }

    protected $fillable = [
        'category_id', 'author_id', 'title', 'slug', 'excerpt', 'content', 'thumbnail_url',
        'tags', 'is_featured', 'is_published', 'published_at', 'view_count',
        'seo_title', 'seo_description', 'seo_keywords', 'read_time_minutes',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
}
