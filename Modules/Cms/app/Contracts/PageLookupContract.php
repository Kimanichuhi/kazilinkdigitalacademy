<?php

namespace Modules\Cms\Contracts;

interface PageLookupContract
{
    /**
     * A published page with its active blocks (ordered), keyed by block
     * `type` => list of block content arrays. Returns null if the page
     * doesn't exist or isn't published.
     */
    public function findBySlug(string $slug): ?array;
}
