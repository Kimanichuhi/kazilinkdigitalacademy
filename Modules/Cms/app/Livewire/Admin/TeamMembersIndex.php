<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\TeamMember;

#[Layout('admin::components.layouts.admin', ['title' => 'Team Members'])]
class TeamMembersIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    protected function defaultForm(): array
    {
        return [
            'full_name' => '', 'title' => '', 'bio' => '', 'avatar_url' => '', 'email' => '',
            'department' => '', 'is_featured' => false, 'is_active' => true,
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
        $member = TeamMember::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'full_name' => $member->full_name,
            'title' => $member->title ?? '',
            'bio' => $member->bio ?? '',
            'avatar_url' => $member->avatar_url ?? '',
            'email' => $member->email ?? '',
            'department' => $member->department ?? '',
            'is_featured' => $member->is_featured,
            'is_active' => $member->is_active,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.full_name' => 'required|string',
        ], [
            'formData.full_name.required' => 'Full name required',
        ]);

        $payload = [
            'full_name' => $this->formData['full_name'],
            'title' => $this->formData['title'] ?: null,
            'bio' => $this->formData['bio'] ?: null,
            'avatar_url' => $this->formData['avatar_url'] ?: null,
            'email' => $this->formData['email'] ?: null,
            'department' => $this->formData['department'] ?: null,
            'is_featured' => $this->formData['is_featured'],
            'is_active' => $this->formData['is_active'],
        ];

        if ($this->editingId) {
            TeamMember::findOrFail($this->editingId)->update($payload);
        } else {
            $payload['order_index'] = TeamMember::count() + 1;
            TeamMember::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        TeamMember::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('cms::livewire.admin.team-members-index', [
            'members' => TeamMember::orderBy('order_index')->paginate(25),
        ]);
    }
}
