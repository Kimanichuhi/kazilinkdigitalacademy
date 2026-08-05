<?php

namespace Modules\Marketing\Contracts;

interface AdvertisementLookupContract
{
    /**
     * Active, currently-published, unexpired ads for a placement, ordered
     * by priority (source: homepage ad banner, keyed on `placement`).
     */
    public function listActiveForPlacement(string $placement, int $limit = 5): array;

    /**
     * Active ads of a given `type` (source: Navbar announcement bar reads
     * type = 'announcement_bar', not placement).
     */
    public function listActiveByType(string $type, int $limit = 1): array;
}
