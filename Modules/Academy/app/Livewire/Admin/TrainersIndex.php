<?php

namespace Modules\Academy\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Academy\Models\Trainer;
use Modules\Core\Support\RoleGroups;

#[Layout('admin::components.layouts.admin', ['title' => 'Trainers'])]
class TrainersIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public bool $showTrashed = false;

    public ?string $editingId = null;

    public array $formData = [];

    protected function defaultForm(): array
    {
        return [
            'full_name' => '', 'title' => '', 'bio' => '', 'avatar_url' => '', 'email' => '',
            'phone' => '', 'specializations' => '', 'is_featured' => false, 'is_active' => true,
        ];
    }

    public function mount(): void
    {
        $this->formData = $this->defaultForm();
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
        $trainer = Trainer::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'full_name' => $trainer->full_name, 'title' => $trainer->title ?? '', 'bio' => $trainer->bio ?? '',
            'avatar_url' => $trainer->avatar_url ?? '', 'email' => $trainer->email ?? '', 'phone' => $trainer->phone ?? '',
            'specializations' => implode(', ', $trainer->specializations ?? []),
            'is_featured' => $trainer->is_featured, 'is_active' => $trainer->is_active,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.full_name' => 'required|string',
        ], [
            'formData.full_name.required' => 'Name required',
        ]);

        $payload = $this->formData;
        $payload['specializations'] = $payload['specializations']
            ? array_values(array_filter(array_map('trim', explode(',', $payload['specializations']))))
            : null;

        if ($this->editingId) {
            Trainer::findOrFail($this->editingId)->update($payload);
        } else {
            Trainer::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        Trainer::findOrFail($id)->delete();
    }

    public function restore(string $id): void
    {
        Trainer::withTrashed()->findOrFail($id)->restore();
    }

    // Permanent deletion is destructive and irreversible — restricted to
    // admin, unlike the regular (now soft) delete above.
    public function forceDelete(string $id): void
    {
        abort_unless(auth()->user()?->hasRole(RoleGroups::ADMIN), 403);

        Trainer::withTrashed()->findOrFail($id)->forceDelete();
    }

    public function render()
    {
        return view('academy::livewire.admin.trainers-index', [
            'trainers' => ($this->showTrashed ? Trainer::onlyTrashed() : Trainer::query())
                ->orderBy('order_index')
                ->paginate(20),
            'canForceDelete' => (bool) auth()->user()?->hasRole(RoleGroups::ADMIN),
        ]);
    }
}
