<?php

namespace Modules\Academy\Contracts;

interface TrainerLookupContract
{
    public function find(string $id): ?array;

    public function listFeatured(int $limit = 4): array;
}
