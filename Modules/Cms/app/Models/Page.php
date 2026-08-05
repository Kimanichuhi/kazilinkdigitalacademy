<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;

class Page extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'is_published',
        'seo_title', 'seo_description', 'seo_keywords', 'og_image_url',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('order_index');
    }
}
