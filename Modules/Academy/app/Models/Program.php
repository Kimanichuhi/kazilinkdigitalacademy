<?php

namespace Modules\Academy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Academy\Database\Factories\ProgramFactory;
use Modules\Core\Models\BaseModel;

class Program extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): ProgramFactory
    {
        return ProgramFactory::new();
    }

    protected $fillable = [
        'category_id', 'title', 'slug', 'subtitle', 'description', 'short_description',
        'thumbnail_url', 'gallery_urls', 'duration_weeks', 'duration_label', 'level',
        'delivery_mode', 'price', 'original_price', 'currency', 'is_featured', 'is_active',
        'is_published', 'rating', 'review_count', 'enrollment_count', 'curriculum',
        'outcomes', 'requirements', 'seo_title', 'seo_description', 'seo_keywords', 'order_index',
    ];

    protected function casts(): array
    {
        return [
            'gallery_urls' => 'array',
            'curriculum' => 'array',
            'outcomes' => 'array',
            'requirements' => 'array',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProgramCategory::class, 'category_id');
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class);
    }
}
