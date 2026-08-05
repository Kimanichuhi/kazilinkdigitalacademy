<?php

namespace Modules\Academy\Contracts;

/**
 * The only surface other modules (Booking, Cms, Marketing, Admin) may use
 * to read program data — never Modules\Academy\Models\Program directly.
 * Programs have no sensitive fields, so lookups return the full public
 * attribute set as a plain array.
 */
interface ProgramLookupContract
{
    public function find(string $id): ?array;

    /**
     * Batch lookup, keyed by id, to avoid N+1 queries when resolving many
     * programs at once (e.g. a student's full booking history).
     *
     * @param  array<int, string>  $ids
     * @return array<string, array>
     */
    public function findMany(array $ids): array;

    public function findBySlug(string $slug): ?array;

    /**
     * Unbounded — for consumers that need the full published set (e.g. the
     * booking wizard's program picker). The public /programs listing page
     * uses listPublishedPaginated() below instead.
     *
     * @param  array{category_id?: string, level?: string, delivery_mode?: string, search?: string, sort?: string}  $filters
     */
    public function listPublished(array $filters = []): array;

    /**
     * Paginated version of listPublished(), same filter shape, for the
     * public /programs page.
     *
     * @param  array{category_id?: string, level?: string, delivery_mode?: string, search?: string, sort?: string}  $filters
     * @return array{data: array, meta: array{current_page: int, per_page: int, total: int, last_page: int}}
     */
    public function listPublishedPaginated(array $filters = [], int $perPage = 12): array;

    public function listFeatured(int $limit = 6): array;

    /**
     * Other published programs in the same category, excluding $excludeId.
     */
    public function listRelated(string $categoryId, string $excludeId, int $limit = 3): array;
}
