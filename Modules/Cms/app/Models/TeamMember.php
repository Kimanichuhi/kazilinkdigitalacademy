<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cms\Database\Factories\TeamMemberFactory;
use Modules\Core\Models\BaseModel;

class TeamMember extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): TeamMemberFactory
    {
        return TeamMemberFactory::new();
    }

    const UPDATED_AT = null;

    protected $fillable = [
        'full_name', 'title', 'bio', 'avatar_url', 'email', 'social_links',
        'department', 'is_featured', 'is_active', 'order_index',
    ];

    protected function casts(): array
    {
        return [
            'avatar_url' => \Modules\Core\Casts\StorageUrl::class,
            'social_links' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
