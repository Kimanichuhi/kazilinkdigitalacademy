<?php

namespace Modules\User\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin::components.layouts.admin', ['title' => 'Students'])]
class StudentsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $students = User::role('student')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('user::livewire.admin.students-index', [
            'students' => $students,
        ]);
    }
}
