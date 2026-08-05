<?php

namespace Modules\Academy\Contracts;

interface ProgramCategoryLookupContract
{
    public function listActive(): array;
}
