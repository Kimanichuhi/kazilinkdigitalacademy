<?php

namespace Modules\Academy\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Models\Cohort;

class CohortLookupService implements CohortLookupContract
{
    public function find(string $id): ?array
    {
        $cohort = Cohort::with(['program', 'trainer'])->find($id);

        return $cohort ? $this->toArray($cohort) : null;
    }

    public function openForProgram(string $programId): array
    {
        return Cohort::with(['program', 'trainer'])
            ->where('program_id', $programId)
            ->whereIn('status', ['open', 'upcoming'])
            ->orderBy('start_date')
            ->get()
            ->map(fn (Cohort $cohort) => $this->toArray($cohort))
            ->all();
    }

    public function upcomingAcrossPrograms(int $limit = 4): array
    {
        // Short TTL cache: backs the homepage on every request. See
        // ProgramLookupService::listFeatured() for the tradeoff note.
        return Cache::remember("cohorts.upcoming.{$limit}", now()->addMinutes(5), fn () => Cohort::with(['program', 'trainer'])
            ->whereIn('status', ['open', 'upcoming'])
            ->orderBy('start_date')
            ->limit($limit)
            ->get()
            ->map(fn (Cohort $cohort) => $this->toArray($cohort))
            ->all());
    }

    public function listPublic(): array
    {
        return Cohort::with(['program', 'trainer'])
            ->whereIn('status', ['open', 'upcoming', 'in_progress'])
            ->orderBy('start_date')
            ->get()
            ->map(fn (Cohort $cohort) => $this->toArray($cohort))
            ->all();
    }

    public function listPublicPaginated(array $filters = [], int $perPage = 12): array
    {
        $query = Cohort::with(['program', 'trainer'])
            ->whereIn('status', ['open', 'upcoming', 'in_progress']);

        if (! empty($filters['search'])) {
            $needle = $filters['search'];
            $query->where(function ($q) use ($needle) {
                $q->where('name', 'like', "%{$needle}%")
                    ->orWhereHas('program', fn ($q) => $q->where('title', 'like', "%{$needle}%"));
            });
        }

        $paginated = $query->orderBy('start_date')->paginate($perPage);

        return [
            'data' => $paginated->getCollection()->map(fn (Cohort $cohort) => $this->toArray($cohort))->all(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
        ];
    }

    public function incrementBookedSeats(string $id, int $by = 1): void
    {
        Cohort::whereKey($id)->increment('booked_seats', $by);
    }

    private function toArray(Cohort $cohort): array
    {
        return [
            ...$cohort->toArray(),
            'seats_left' => $cohort->seatsLeft(),
            'program' => $cohort->program?->toArray(),
            'trainer' => $cohort->trainer?->toArray(),
        ];
    }
}
