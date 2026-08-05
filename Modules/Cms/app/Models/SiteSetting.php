<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;

class SiteSetting extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['key', 'value', 'value_json', 'category', 'label', 'description', 'updated_at'];

    protected function casts(): array
    {
        return [
            'value_json' => 'array',
            'updated_at' => 'datetime',
        ];
    }
}
