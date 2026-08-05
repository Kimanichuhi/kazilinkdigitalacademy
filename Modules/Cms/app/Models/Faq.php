<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Cms\Database\Factories\FaqFactory;
use Modules\Core\Models\BaseModel;

class Faq extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): FaqFactory
    {
        return FaqFactory::new();
    }

    protected $fillable = ['category', 'question', 'answer', 'order_index', 'is_published', 'is_popular'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_popular' => 'boolean'];
    }
}
