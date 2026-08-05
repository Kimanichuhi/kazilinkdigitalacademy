<?php

namespace Modules\Marketing\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Marketing\Contracts\CtaLookupContract;
use Modules\Marketing\Models\Cta;

class CtaLookupService implements CtaLookupContract
{
    public function activeForPlacement(string $placement): ?array
    {
        // Short TTL cache: backs the homepage on every request. See
        // Modules\Academy\Services\ProgramLookupService::listFeatured()
        // for the tradeoff note.
        return Cache::remember("ctas.placement.{$placement}", now()->addMinutes(5), fn () => Cta::query()
            ->where('is_active', true)
            ->whereJsonContains('placement', $placement)
            ->orderByDesc('priority')
            ->first()
            ?->toArray());
    }
}
