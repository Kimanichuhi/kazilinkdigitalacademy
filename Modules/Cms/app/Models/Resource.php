<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cms\Database\Factories\ResourceFactory;
use Modules\Core\Models\BaseModel;

class Resource extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): ResourceFactory
    {
        return ResourceFactory::new();
    }

    protected $fillable = [
        'program_id', 'title', 'description', 'type', 'file_url', 'thumbnail_url',
        'is_free', 'is_published', 'download_count', 'tags', 'order_index',
        'is_paid', 'price', 'currency', 'download_limit',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_free' => 'boolean',
            'is_published' => 'boolean',
            'is_paid' => 'boolean',
            'price' => 'decimal:2',
        ];
    }
}
