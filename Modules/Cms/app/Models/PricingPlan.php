<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;

class PricingPlan extends BaseModel
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name', 'tag', 'price', 'currency', 'period', 'description', 'features',
        'cta_text', 'cta_link', 'is_highlighted', 'is_published', 'order_index',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'is_highlighted' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
