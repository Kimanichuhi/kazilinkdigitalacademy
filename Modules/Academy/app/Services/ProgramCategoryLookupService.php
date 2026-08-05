<?php

namespace Modules\Academy\Services;

use Modules\Academy\Contracts\ProgramCategoryLookupContract;
use Modules\Academy\Models\ProgramCategory;

class ProgramCategoryLookupService implements ProgramCategoryLookupContract
{
    public function listActive(): array
    {
        return ProgramCategory::query()
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get()
            ->map->toArray()
            ->all();
    }
}
