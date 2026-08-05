<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Models\BlogPost;

#[Layout('admin::components.layouts.admin', ['title' => 'Blog'])]
class BlogPostsIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    protected function defaultForm(): array
    {
        return [
            'title' => '', 'slug' => '', 'category_id' => '', 'excerpt' => '', 'content' => '',
            'thumbnail_url' => '', 'tags' => '', 'read_time_minutes' => '5',
            'is_published' => false, 'is_featured' => false,
        ];
    }

    public function mount(): void
    {
        $this->formData = $this->defaultForm();
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->formData = $this->defaultForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $post = BlogPost::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'title' => $post->title,
            'slug' => $post->slug,
            'category_id' => $post->category_id ?? '',
            'excerpt' => $post->excerpt ?? '',
            'content' => $post->content ?? '',
            'thumbnail_url' => $post->thumbnail_url ?? '',
            'tags' => $post->tags ? implode(', ', $post->tags) : '',
            'read_time_minutes' => (string) ($post->read_time_minutes ?? ''),
            'is_published' => $post->is_published,
            'is_featured' => $post->is_featured,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.title' => 'required|string',
            'formData.slug' => 'required|string',
        ], [
            'formData.title.required' => 'Title and slug required',
            'formData.slug.required' => 'Title and slug required',
        ]);

        $tags = collect(explode(',', $this->formData['tags']))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        $payload = [
            'title' => $this->formData['title'],
            'slug' => $this->formData['slug'],
            'category_id' => $this->formData['category_id'] ?: null,
            'excerpt' => $this->formData['excerpt'] ?: null,
            'content' => $this->formData['content'] ?: null,
            'thumbnail_url' => $this->formData['thumbnail_url'] ?: null,
            'tags' => $tags ?: null,
            'read_time_minutes' => $this->formData['read_time_minutes'] !== '' ? (int) $this->formData['read_time_minutes'] : null,
            'is_published' => $this->formData['is_published'],
            'is_featured' => $this->formData['is_featured'],
            'published_at' => $this->formData['is_published'] ? now() : null,
        ];

        if ($this->editingId) {
            BlogPost::findOrFail($this->editingId)->update($payload);
        } else {
            BlogPost::create($payload);
        }

        $this->showForm = false;
    }

    public function togglePublish(string $id): void
    {
        $post = BlogPost::findOrFail($id);
        $post->update([
            'is_published' => ! $post->is_published,
            'published_at' => ! $post->is_published ? now() : null,
        ]);
    }

    public function delete(string $id): void
    {
        BlogPost::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('cms::livewire.admin.blog-posts-index', [
            'posts' => BlogPost::with('category')->orderByDesc('created_at')->paginate(20),
            'categories' => BlogCategory::orderBy('order_index')->get(),
        ]);
    }
}
