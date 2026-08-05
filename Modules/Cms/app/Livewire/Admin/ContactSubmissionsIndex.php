<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\ContactSubmission;

#[Layout('admin::components.layouts.admin', ['title' => 'Contact Messages'])]
class ContactSubmissionsIndex extends Component
{
    use WithPagination;

    public ?string $viewingId = null;

    public string $notes = '';

    public function view(string $id): void
    {
        $submission = ContactSubmission::findOrFail($id);
        $this->viewingId = $id;
        $this->notes = $submission->admin_notes ?? '';

        if ($submission->status === 'new') {
            $submission->update(['status' => 'read']);
        }
    }

    public function close(): void
    {
        $this->viewingId = null;
        $this->notes = '';
    }

    public function updateStatus(string $id, string $status): void
    {
        ContactSubmission::findOrFail($id)->update(['status' => $status]);
    }

    public function saveNotes(): void
    {
        if (! $this->viewingId) {
            return;
        }

        ContactSubmission::findOrFail($this->viewingId)->update(['admin_notes' => $this->notes]);
    }

    public function delete(string $id): void
    {
        ContactSubmission::findOrFail($id)->delete();

        if ($this->viewingId === $id) {
            $this->close();
        }
    }

    public function render()
    {
        return view('cms::livewire.admin.contact-submissions-index', [
            'submissions' => ContactSubmission::orderByDesc('created_at')->paginate(20),
            'viewing' => $this->viewingId ? ContactSubmission::find($this->viewingId) : null,
        ]);
    }
}
