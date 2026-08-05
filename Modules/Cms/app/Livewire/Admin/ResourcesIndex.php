<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\Resource;

#[Layout('admin::components.layouts.admin', ['title' => 'Resources'])]
class ResourcesIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    protected function defaultForm(): array
    {
        return [
            'title' => '', 'description' => '', 'type' => 'pdf', 'file_url' => '', 'tags' => '',
            'is_free' => true, 'is_published' => true,
            'is_paid' => false, 'price' => '195.00', 'download_limit' => '',
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
        $resource = Resource::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'title' => $resource->title,
            'description' => $resource->description ?? '',
            'type' => $resource->type,
            'file_url' => $resource->file_url ?? '',
            'tags' => $resource->tags ? implode(', ', $resource->tags) : '',
            'is_free' => $resource->is_free,
            'is_published' => $resource->is_published,
            'is_paid' => $resource->is_paid,
            'price' => (string) $resource->price,
            'download_limit' => $resource->download_limit ? (string) $resource->download_limit : '',
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.title' => 'required|string',
        ], [
            'formData.title.required' => 'Title required',
        ]);

        $tags = collect(explode(',', $this->formData['tags']))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        $payload = [
            'title' => $this->formData['title'],
            'description' => $this->formData['description'] ?: null,
            'type' => $this->formData['type'],
            'file_url' => $this->formData['file_url'] ?: null,
            'tags' => $tags ?: null,
            'is_free' => $this->formData['is_free'],
            'is_published' => $this->formData['is_published'],
            'is_paid' => $this->formData['is_paid'],
            'price' => $this->formData['price'] !== '' ? (float) $this->formData['price'] : 195.00,
            'download_limit' => $this->formData['download_limit'] !== '' ? (int) $this->formData['download_limit'] : null,
        ];

        if ($this->editingId) {
            Resource::findOrFail($this->editingId)->update($payload);
        } else {
            $payload['order_index'] = Resource::count() + 1;
            Resource::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        Resource::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('cms::livewire.admin.resources-index', [
            'resources' => Resource::orderBy('order_index')->paginate(20),
        ]);
    }
}
