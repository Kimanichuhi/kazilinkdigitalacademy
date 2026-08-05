<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Cms\Database\Factories\BlogCategoryFactory;
use Modules\Core\Models\BaseModel;

class BlogCategory extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): BlogCategoryFactory
    {
        return BlogCategoryFactory::new();
    }

    const UPDATED_AT = null;

    protected $fillable = ['name', 'slug', 'description', 'color', 'order_index', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}
