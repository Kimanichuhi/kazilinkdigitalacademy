<?php

namespace Modules\Marketing\Contracts;

interface CtaLookupContract
{
    public function activeForPlacement(string $placement): ?array;
}
