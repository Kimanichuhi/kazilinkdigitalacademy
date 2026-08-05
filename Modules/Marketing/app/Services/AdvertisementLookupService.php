<?php

namespace Modules\Marketing\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Marketing\Contracts\AdvertisementLookupContract;
use Modules\Marketing\Models\Advertisement;

class AdvertisementLookupService implements AdvertisementLookupContract
{
    public function listActiveForPlacement(string $placement, int $limit = 5): array
    {
        // Short TTL cache: backs the homepage on every request. See
        // Modules\Academy\Services\ProgramLookupService::listFeatured()
        // for the tradeoff note. Publish/expiry windows mean a stale hit
        // could show an ad up to 5 minutes past/before its window, an
        // acceptable margin for marketing content.
        return Cache::remember("ads.placement.{$placement}.{$limit}", now()->addMinutes(5), fn () => Advertisement::query()
            ->where('status', 'active')
            ->whereJsonContains('placement', $placement)
            ->where(function ($q) {
                $q->whereNull('publish_date')->orWhere('publish_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            })
            ->orderByDesc('priority')
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all());
    }

    public function listActiveByType(string $type, int $limit = 1): array
    {
        return Cache::remember("ads.type.{$type}.{$limit}", now()->addMinutes(5), fn () => Advertisement::query()
            ->where('type', $type)
            ->where('status', 'active')
            ->orderByDesc('priority')
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all());
    }
}
