<?php

namespace Modules\Cms\Contracts;

interface BlogLookupContract
{
    /**
     * Unbounded — for consumers that need the full published set (e.g. the
     * related-posts lookup on the post-show page). The public /blog listing
     * page uses listPublishedPaginated() below instead.
     */
    public function listPublished(): array;

    /**
     * Paginated, SQL-filtered listing for the public /blog page.
     *
     * @param  array{category_id?: string, search?: string}  $filters
     * @return array{data: array, meta: array{current_page: int, per_page: int, total: int, last_page: int}}
     */
    public function listPublishedPaginated(array $filters = [], int $perPage = 12): array;

    /**
     * The single post pinned as the /blog page's hero card, or null.
     * A dedicated single-row lookup rather than deriving it from
     * listPublished(), so the unfiltered listing page never has to pull
     * every published post just to find this one.
     */
    public function findFeatured(): ?array;

    public function listCategories(): array;

    public function findBySlug(string $slug): ?array;

    public function incrementViewCount(string $id): void;
}
