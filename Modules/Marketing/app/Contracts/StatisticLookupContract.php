<?php

namespace Modules\Marketing\Contracts;

interface StatisticLookupContract
{
    public function listActive(): array;
}
