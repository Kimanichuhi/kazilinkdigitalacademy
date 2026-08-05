<?php

namespace Modules\Marketing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;
use Modules\Marketing\Database\Factories\CtaFactory;

class Cta extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): CtaFactory
    {
        return CtaFactory::new();
    }

    protected $fillable = [
        'name', 'title', 'subtitle', 'description', 'background_color', 'background_image_url',
        'button_one_text', 'button_one_link', 'button_one_style',
        'button_two_text', 'button_two_link', 'button_two_style',
        'placement', 'priority', 'is_active', 'publish_date', 'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'placement' => 'array',
            'is_active' => 'boolean',
            'publish_date' => 'datetime',
            'expiry_date' => 'datetime',
        ];
    }
}
