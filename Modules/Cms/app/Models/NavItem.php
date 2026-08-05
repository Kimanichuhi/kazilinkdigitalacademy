<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;

class NavItem extends BaseModel
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'url', 'icon', 'target', 'order_index', 'is_active', 'badge',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavItem::class, 'parent_id')->orderBy('order_index');
    }
}
