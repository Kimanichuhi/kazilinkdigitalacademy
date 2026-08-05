<?php

namespace Modules\Cms\Contracts;

interface ResourceLookupContract
{
    public function find(string $id): ?array;

    public function listPublished(?string $type = null): array;

    /**
     * Paginated, SQL-filtered listing for the public /resources page.
     *
     * @param  array{type?: string, search?: string}  $filters
     * @return array{data: array, meta: array{current_page: int, per_page: int, total: int, last_page: int}}
     */
    public function listPublishedPaginated(array $filters = [], int $perPage = 12): array;

    public function incrementDownloads(string $id): void;
}
