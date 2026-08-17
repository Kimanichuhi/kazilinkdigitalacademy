<?php

namespace Modules\Academy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Academy\Database\Factories\TrainerFactory;
use Modules\Core\Models\BaseModel;

class Trainer extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): TrainerFactory
    {
        return TrainerFactory::new();
    }

    protected $fillable = [
        'profile_id', 'full_name', 'title', 'bio', 'avatar_url', 'email', 'phone',
        'specializations', 'social_links', 'rating', 'review_count',
        'is_featured', 'is_active', 'order_index',
    ];

    protected function casts(): array
    {
        return [
            'avatar_url' => \Modules\Core\Casts\StorageUrl::class,
            'specializations' => 'array',
            'social_links' => 'array',
            'rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class);
    }
}
