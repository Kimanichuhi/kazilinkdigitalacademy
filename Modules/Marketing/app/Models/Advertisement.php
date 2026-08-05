<?php

namespace Modules\Marketing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;
use Modules\Marketing\Database\Factories\AdvertisementFactory;

class Advertisement extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): AdvertisementFactory
    {
        return AdvertisementFactory::new();
    }

    protected $fillable = [
        'campaign_name', 'title', 'subtitle', 'description', 'type', 'placement',
        'desktop_image_url', 'mobile_image_url', 'video_url', 'cta_text', 'cta_link',
        'button_style', 'background_color', 'overlay_color', 'animation', 'priority',
        'target_audience', 'status', 'publish_date', 'expiry_date', 'view_count', 'click_count',
    ];

    protected function casts(): array
    {
        return [
            'placement' => 'array',
            'target_audience' => 'array',
            'publish_date' => 'datetime',
            'expiry_date' => 'datetime',
        ];
    }
}
