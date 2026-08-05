<?php

namespace Modules\Academy\Contracts;

interface CohortLookupContract
{
    public function find(string $id): ?array;

    /**
     * Open/upcoming cohorts for a given program, ordered by start date, with
     * `program` and `trainer` arrays embedded (denormalized read, no join
     * across module boundaries).
     */
    public function openForProgram(string $programId): array;

    /**
     * Open/upcoming cohorts across all programs, for the homepage.
     */
    public function upcomingAcrossPrograms(int $limit = 4): array;

    /**
     * Open/upcoming/in-progress cohorts across all programs, unbounded.
     * listPublicPaginated() below is used by the public /cohorts page itself.
     */
    public function listPublic(): array;

    /**
     * Paginated, SQL-filtered version of listPublic() for the public
     * /cohorts page.
     *
     * @param  array{search?: string}  $filters
     * @return array{data: array, meta: array{current_page: int, per_page: int, total: int, last_page: int}}
     */
    public function listPublicPaginated(array $filters = [], int $perPage = 12): array;

    public function incrementBookedSeats(string $id, int $by = 1): void;
}
