<?php

namespace Modules\Cms\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Cms\Contracts\TestimonialLookupContract;
use Modules\Cms\Models\Testimonial;

class TestimonialLookupService implements TestimonialLookupContract
{
    public function listFeatured(int $limit = 6): array
    {
        // Short TTL cache: backs the homepage on every request. See
        // ProgramLookupService::listFeatured() for the tradeoff note.
        return Cache::remember("testimonials.featured.{$limit}", now()->addMinutes(5), fn () => Testimonial::query()
            ->where('is_featured', true)
            ->where('is_published', true)
            ->orderBy('order_index')
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all());
    }

    public function listForProgram(string $programId, int $limit = 4): array
    {
        return Testimonial::query()
            ->where('program_id', $programId)
            ->where('is_published', true)
            ->orderBy('order_index')
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all();
    }
}
