<?php

namespace Modules\Academy\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Academy\Models\Program;
use Modules\Academy\Models\ProgramCategory;
use Modules\Core\Support\RoleGroups;

#[Layout('admin::components.layouts.admin', ['title' => 'Programs'])]
class ProgramsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public bool $showTrashed = false;

    public ?string $editingId = null;

    public array $formData = [
        'title' => '', 'slug' => '', 'subtitle' => '', 'short_description' => '', 'description' => '',
        'thumbnail_url' => '', 'duration_weeks' => '', 'duration_label' => '', 'level' => 'beginner',
        'delivery_mode' => 'online', 'price' => '', 'original_price' => '', 'currency' => 'KES',
        'category_id' => '', 'is_featured' => false, 'is_published' => false,
    ];

    protected function defaultForm(): array
    {
        return [
            'title' => '', 'slug' => '', 'subtitle' => '', 'short_description' => '', 'description' => '',
            'thumbnail_url' => '', 'duration_weeks' => '', 'duration_label' => '', 'level' => 'beginner',
            'delivery_mode' => 'online', 'price' => '', 'original_price' => '', 'currency' => 'KES',
            'category_id' => '', 'is_featured' => false, 'is_published' => false,
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingShowTrashed(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->formData = $this->defaultForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $program = Program::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'title' => $program->title, 'slug' => $program->slug, 'subtitle' => $program->subtitle ?? '',
            'short_description' => $program->short_description ?? '', 'description' => $program->description ?? '',
            'thumbnail_url' => $program->thumbnail_url ?? '', 'duration_weeks' => (string) ($program->duration_weeks ?? ''),
            'duration_label' => $program->duration_label ?? '', 'level' => $program->level,
            'delivery_mode' => $program->delivery_mode, 'price' => (string) ($program->price ?? ''),
            'original_price' => (string) ($program->original_price ?? ''), 'currency' => $program->currency,
            'category_id' => $program->category_id ?? '', 'is_featured' => $program->is_featured,
            'is_published' => $program->is_published,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.title' => 'required|string',
            'formData.slug' => 'required|string',
        ], [
            'formData.title.required' => 'Title and slug are required',
            'formData.slug.required' => 'Title and slug are required',
        ]);

        $payload = $this->formData;
        $payload['duration_weeks'] = $payload['duration_weeks'] !== '' ? (int) $payload['duration_weeks'] : null;
        $payload['price'] = $payload['price'] !== '' ? (float) $payload['price'] : null;
        $payload['original_price'] = $payload['original_price'] !== '' ? (float) $payload['original_price'] : null;
        $payload['category_id'] = $payload['category_id'] ?: null;

        if ($this->editingId) {
            Program::findOrFail($this->editingId)->update($payload);
        } else {
            Program::create($payload);
        }

        $this->showForm = false;
    }

    public function togglePublish(string $id): void
    {
        $program = Program::findOrFail($id);
        $program->update(['is_published' => ! $program->is_published]);
    }

    public function delete(string $id): void
    {
        Program::findOrFail($id)->delete();
    }

    public function restore(string $id): void
    {
        Program::withTrashed()->findOrFail($id)->restore();
    }

    // Permanent deletion is destructive and irreversible — restricted to
    // super_admin, unlike the regular (now soft) delete above.
    public function forceDelete(string $id): void
    {
        abort_unless(auth()->user()?->hasRole(RoleGroups::SUPER_ADMIN), 403);

        Program::withTrashed()->findOrFail($id)->forceDelete();
    }

    public function render()
    {
        $programs = ($this->showTrashed ? Program::onlyTrashed() : Program::query())
            ->with('category')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('order_index')
            ->paginate(20);

        return view('academy::livewire.admin.programs-index', [
            'programs' => $programs,
            'categories' => ProgramCategory::orderBy('order_index')->get(),
            'canForceDelete' => (bool) auth()->user()?->hasRole(RoleGroups::SUPER_ADMIN),
        ]);
    }
}
