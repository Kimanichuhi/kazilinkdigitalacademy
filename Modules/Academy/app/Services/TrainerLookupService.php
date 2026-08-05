<?php

namespace Modules\Academy\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Academy\Contracts\TrainerLookupContract;
use Modules\Academy\Models\Trainer;

class TrainerLookupService implements TrainerLookupContract
{
    public function find(string $id): ?array
    {
        return Trainer::find($id)?->toArray();
    }

    public function listFeatured(int $limit = 4): array
    {
        // Short TTL cache: backs the homepage on every request. See
        // ProgramLookupService::listFeatured() for the tradeoff note.
        return Cache::remember("trainers.featured.{$limit}", now()->addMinutes(5), fn () => Trainer::query()
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all());
    }
}
