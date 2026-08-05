<?php

namespace Modules\Academy\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Academy\Models\Program;

class ProgramLookupService implements ProgramLookupContract
{
    public function find(string $id): ?array
    {
        $program = Program::find($id);

        return $program?->toArray();
    }

    public function findMany(array $ids): array
    {
        return Program::query()
            ->whereIn('id', array_unique($ids))
            ->get()
            ->keyBy('id')
            ->map->toArray()
            ->all();
    }

    public function findBySlug(string $slug): ?array
    {
        $program = Program::where('slug', $slug)->where('is_published', true)->first();

        return $program?->toArray();
    }

    public function listPublished(array $filters = []): array
    {
        return $this->publishedQuery($filters)->get()->map->toArray()->all();
    }

    public function listPublishedPaginated(array $filters = [], int $perPage = 12): array
    {
        $paginated = $this->publishedQuery($filters)->paginate($perPage);

        return [
            'data' => $paginated->getCollection()->map->toArray()->all(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
        ];
    }

    private function publishedQuery(array $filters)
    {
        $query = Program::query()->where('is_published', true)->where('is_active', true);

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }
        if (! empty($filters['delivery_mode'])) {
            $query->where('delivery_mode', $filters['delivery_mode']);
        }
        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        match ($filters['sort'] ?? 'order_index') {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            'enrollment_count' => $query->orderBy('enrollment_count', 'desc'),
            default => $query->orderBy('order_index'),
        };

        return $query;
    }

    public function listFeatured(int $limit = 6): array
    {
        // 5-minute TTL: this backs the homepage, hit on every visitor
        // request. Admin edits to featured programs take up to 5 minutes
        // to appear publicly — an accepted tradeoff, not a bug.
        return Cache::remember("programs.featured.{$limit}", now()->addMinutes(5), fn () => Program::query()
            ->where('is_featured', true)
            ->where('is_published', true)
            ->orderBy('order_index')
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all());
    }

    public function listRelated(string $categoryId, string $excludeId, int $limit = 3): array
    {
        return Program::query()
            ->where('is_published', true)
            ->where('category_id', $categoryId)
            ->whereKeyNot($excludeId)
            ->limit($limit)
            ->get()
            ->map->toArray()
            ->all();
    }
}
