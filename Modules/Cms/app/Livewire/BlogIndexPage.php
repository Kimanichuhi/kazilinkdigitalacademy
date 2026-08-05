<?php

namespace Modules\Cms\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Contracts\BlogLookupContract;

#[Layout('core::components.layouts.public', ['title' => 'Blog'])]
class BlogIndexPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryId = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function render(BlogLookupContract $blog)
    {
        // The featured hero card only makes sense on the unfiltered
        // listing — hidden while searching/filtering by category.
        $featured = $this->search === '' && $this->categoryId === '' ? $blog->findFeatured() : null;

        $result = $blog->listPublishedPaginated([
            'category_id' => $this->categoryId ?: null,
            'search' => $this->search ?: null,
        ], 12);

        $posts = $featured
            ? array_values(array_filter($result['data'], fn ($post) => $post['id'] !== $featured['id']))
            : $result['data'];

        $paginator = new LengthAwarePaginator(
            $posts,
            $result['meta']['total'],
            $result['meta']['per_page'],
            $result['meta']['current_page'],
        );

        return view('cms::livewire.blog-index-page', [
            'featured' => $featured,
            'posts' => $paginator,
            'categories' => $blog->listCategories(),
        ]);
    }
}
