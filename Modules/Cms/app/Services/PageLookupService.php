<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\PageLookupContract;
use Modules\Cms\Models\Page;

class PageLookupService implements PageLookupContract
{
    public function findBySlug(string $slug): ?array
    {
        $page = Page::with(['blocks' => fn ($query) => $query->where('is_active', true)])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $page) {
            return null;
        }

        return [
            ...$page->only(['id', 'title', 'slug', 'description', 'seo_title', 'seo_description', 'seo_keywords', 'og_image_url']),
            'blocks' => $page->blocks->groupBy('type')->map->values()->toArray(),
        ];
    }
}
