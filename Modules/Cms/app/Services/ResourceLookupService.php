<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\ResourceLookupContract;
use Modules\Cms\Models\Resource;

class ResourceLookupService implements ResourceLookupContract
{
    public function find(string $id): ?array
    {
        $resource = Resource::where('is_published', true)->find($id);

        return $resource?->toArray();
    }

    public function listPublished(?string $type = null): array
    {
        return Resource::query()
            ->where('is_published', true)
            ->when($type, fn ($query) => $query->where('type', $type))
            ->orderBy('order_index')
            ->get()
            ->toArray();
    }

    public function listPublishedPaginated(array $filters = [], int $perPage = 12): array
    {
        $query = Resource::query()->where('is_published', true);

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['search'])) {
            $needle = $filters['search'];
            $query->where(function ($q) use ($needle) {
                $q->where('title', 'like', "%{$needle}%")
                    ->orWhere('description', 'like', "%{$needle}%");
            });
        }

        $paginated = $query->orderBy('order_index')->paginate($perPage);

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

    public function incrementDownloads(string $id): void
    {
        Resource::whereKey($id)->increment('download_count');
    }
}
