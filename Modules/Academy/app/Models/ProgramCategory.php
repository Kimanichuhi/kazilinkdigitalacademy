<?php

namespace Modules\Academy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Academy\Database\Factories\ProgramCategoryFactory;
use Modules\Core\Models\BaseModel;

class ProgramCategory extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): ProgramCategoryFactory
    {
        return ProgramCategoryFactory::new();
    }

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color', 'order_index', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order_index' => 'integer',
        ];
    }

    const UPDATED_AT = null;

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'category_id');
    }
}
