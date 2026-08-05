<?php

namespace Modules\Marketing\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Marketing\Contracts\StatisticLookupContract;
use Modules\Marketing\Models\Statistic;

class StatisticLookupService implements StatisticLookupContract
{
    public function listActive(): array
    {
        // Short TTL cache: backs the homepage on every request. See
        // Modules\Academy\Services\ProgramLookupService::listFeatured()
        // for the tradeoff note.
        return Cache::remember('statistics.active', now()->addMinutes(5), fn () => Statistic::query()
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get()
            ->map->toArray()
            ->all());
    }
}
