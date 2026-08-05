<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\BlogLookupContract;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Models\BlogPost;

class BlogLookupService implements BlogLookupContract
{
    public function listPublished(): array
    {
        return BlogPost::with('category')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->get()
            ->toArray();
    }

    public function listPublishedPaginated(array $filters = [], int $perPage = 12): array
    {
        $query = BlogPost::with('category')->where('is_published', true);

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $needle = $filters['search'];
            $query->where(function ($q) use ($needle) {
                $q->where('title', 'like', "%{$needle}%")
                    ->orWhere('excerpt', 'like', "%{$needle}%");
            });
        }

        $paginated = $query->orderByDesc('published_at')->paginate($perPage);

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

    public function findFeatured(): ?array
    {
        $post = BlogPost::with('category')
            ->where('is_published', true)
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->first();

        return $post?->toArray();
    }

    public function listCategories(): array
    {
        return BlogCategory::where('is_active', true)
            ->orderBy('order_index')
            ->get()
            ->toArray();
    }

    public function findBySlug(string $slug): ?array
    {
        $post = BlogPost::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        return $post?->toArray();
    }

    public function incrementViewCount(string $id): void
    {
        BlogPost::whereKey($id)->increment('view_count');
    }
}
