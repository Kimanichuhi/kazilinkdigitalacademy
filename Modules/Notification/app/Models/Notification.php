<?php

namespace Modules\Notification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\BaseModel;
use Modules\Notification\Database\Factories\NotificationFactory;

class Notification extends BaseModel
{
    use HasFactory;

    const UPDATED_AT = null;

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }

    protected $fillable = ['user_id', 'title', 'message', 'type', 'link', 'is_read'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
