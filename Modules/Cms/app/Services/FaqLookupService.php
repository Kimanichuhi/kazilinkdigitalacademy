<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\FaqLookupContract;
use Modules\Cms\Models\Faq;

class FaqLookupService implements FaqLookupContract
{
    public function listPublished(int $limit = 8): array
    {
        return Faq::query()
            ->where('is_published', true)
            ->orderBy('order_index')
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all();
    }
}
