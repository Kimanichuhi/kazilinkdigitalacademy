<?php

namespace Modules\Cms\Contracts;

interface TestimonialLookupContract
{
    public function listFeatured(int $limit = 6): array;

    public function listForProgram(string $programId, int $limit = 4): array;
}
