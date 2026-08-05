<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cms\Database\Factories\PartnerFactory;
use Modules\Core\Models\BaseModel;

class Partner extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): PartnerFactory
    {
        return PartnerFactory::new();
    }

    const UPDATED_AT = null;

    protected $fillable = ['name', 'logo_url', 'website_url', 'description', 'is_active', 'order_index'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
