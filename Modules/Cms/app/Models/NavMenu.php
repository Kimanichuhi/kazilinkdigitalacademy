<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;

class NavMenu extends BaseModel
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = ['name', 'location', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(NavItem::class, 'menu_id')->whereNull('parent_id')->orderBy('order_index');
    }
}
