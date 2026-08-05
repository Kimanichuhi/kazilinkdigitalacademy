<?php

namespace Modules\Marketing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;
use Modules\Marketing\Database\Factories\StatisticFactory;

class Statistic extends BaseModel
{
    use HasFactory;

    const UPDATED_AT = null;

    protected static function newFactory(): StatisticFactory
    {
        return StatisticFactory::new();
    }

    protected $fillable = ['label', 'value', 'icon', 'description', 'order_index', 'is_active', 'key'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
