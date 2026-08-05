<?php

namespace Modules\Cms\Contracts;

interface FaqLookupContract
{
    public function listPublished(int $limit = 8): array;
}
